<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use App\Models\Lead;
use App\UseCases\Messaging\SendFollowUpSms;
use App\UseCases\Webhooks\ProcessMessageBirdSmsWebhook;
use App\UseCases\Webhooks\ProcessMessageBirdVoiceWebhook;
use App\UseCases\Webhooks\ProcessTwilioSmsWebhook;
use App\UseCases\Webhooks\ProcessTwilioVoiceWebhook;
use App\UseCases\Webhooks\ProcessVonageSmsWebhook;
use App\UseCases\Webhooks\ProcessVonageVoiceWebhook;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SimulateMissedCallFlow extends Command
{
    protected $signature = 'simulate:missed-call
                            {--clinic= : Clinic ID (uses first clinic if not specified)}
                            {--phone= : Caller phone number (generates random if not specified)}
                            {--provider=twilio : Provider to simulate (twilio, vonage, messagebird)}
                            {--skip-sms : Skip sending the follow-up SMS}
                            {--with-response : Simulate patient response}';

    protected $description = 'Simulate the complete missed call → follow-up → response flow';

    public function handle(
        ProcessTwilioVoiceWebhook $twilioVoice,
        ProcessTwilioSmsWebhook $twilioSms,
        ProcessVonageVoiceWebhook $vonageVoice,
        ProcessVonageSmsWebhook $vonageSms,
        ProcessMessageBirdVoiceWebhook $messageBirdVoice,
        ProcessMessageBirdSmsWebhook $messageBirdSms,
        SendFollowUpSms $sendFollowUp,
    ): int {
        $this->info('');
        $this->info('🦷 Missed Revenue Recovery - Flow Simulator');
        $this->info('==========================================');
        $this->info('');

        $provider = $this->option('provider');
        $validProviders = ['twilio', 'vonage', 'messagebird'];

        if (!in_array($provider, $validProviders)) {
            $this->error("Invalid provider: {$provider}. Valid options: " . implode(', ', $validProviders));
            return 1;
        }

        // Get clinic
        $clinicId = $this->option('clinic');
        $clinic = $clinicId ? Clinic::find($clinicId) : Clinic::first();

        if (!$clinic) {
            $this->error('No clinic found. Run: php artisan db:seed');
            return 1;
        }

        $this->info("📍 Clinic: {$clinic->name}");
        $this->info("🔌 Provider: {$provider}");

        // Get clinic phone number for this provider
        $clinicPhone = ClinicPhoneNumber::where('clinic_id', $clinic->id)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (!$clinicPhone) {
            $this->error("No active {$provider} phone number for this clinic.");
            $this->line('');
            $this->line('Available numbers:');
            ClinicPhoneNumber::where('clinic_id', $clinic->id)->get()->each(function ($p) {
                $status = $p->is_active ? '✅' : '❌';
                $this->line("  {$status} {$p->phone_number} ({$p->provider})");
            });
            return 1;
        }

        $this->info("📞 Clinic Phone: {$clinicPhone->phone_number}");

        // Generate or use caller phone
        $callerPhone = $this->option('phone') ?? $this->generateRandomPhone();
        $this->info("👤 Caller Phone: {$callerPhone}");
        $this->info('');

        // Step 1: Simulate missed call webhook
        $this->info('─────────────────────────────────────────');
        $this->info("STEP 1: Simulating {$provider} Voice Webhook (Missed Call)");
        $this->info('─────────────────────────────────────────');

        $voicePayload = $this->buildVoicePayload($provider, $callerPhone, $clinicPhone->phone_number);
        $callId = $voicePayload['_id'];
        unset($voicePayload['_id']);

        $this->line("  ID: {$callId}");
        $this->line("  Status: no-answer");

        try {
            match ($provider) {
                'twilio' => $twilioVoice->execute($voicePayload),
                'vonage' => $vonageVoice->execute($voicePayload),
                'messagebird' => $messageBirdVoice->execute($voicePayload),
            };
            $this->info('  ✅ Webhook processed successfully');
        } catch (\Exception $e) {
            $this->error("  ❌ Error: {$e->getMessage()}");
            return 1;
        }

        // Find the created lead
        $lead = Lead::where('clinic_id', $clinic->id)
            ->whereHas('caller', fn($q) => $q->where('phone', $callerPhone))
            ->latest()
            ->first();

        if (!$lead) {
            $this->error('  ❌ Lead was not created');
            return 1;
        }

        $this->info("  📋 Lead created: #{$lead->id}");
        $this->info("  📊 Stage: {$lead->stage->value}");
        $this->info("  💬 Conversation: #{$lead->conversation->id}");
        $this->info('');

        // Step 2: Send follow-up SMS
        if (!$this->option('skip-sms')) {
            $this->info('─────────────────────────────────────────');
            $this->info('STEP 2: Sending Follow-up SMS');
            $this->info('─────────────────────────────────────────');

            try {
                $message = $sendFollowUp->execute($lead->id);

                if ($message) {
                    $this->info("  ✅ SMS queued successfully");
                    $this->line("  📱 Message ID: #{$message->id}");
                    $this->line("  📝 Body: {$message->body}");
                    $this->line("  📊 Status: {$message->status}");

                    $lead->refresh();
                    $this->info("  📊 Lead Stage: {$lead->stage->value}");
                } else {
                    $this->warn('  ⚠️  SMS not sent (no active template or lead not in NEW stage)');
                }
            } catch (\Exception $e) {
                $this->warn("  ⚠️  SMS sending failed: {$e->getMessage()}");
                $this->line("  (Expected if {$provider} credentials are not configured)");
            }
            $this->info('');
        }

        // Step 3: Simulate patient response
        if ($this->option('with-response')) {
            $this->info('─────────────────────────────────────────');
            $this->info('STEP 3: Simulating Patient Response');
            $this->info('─────────────────────────────────────────');

            $responseBody = 'Hola, me interesa agendar una cita para la próxima semana.';
            $smsPayload = $this->buildSmsPayload($provider, $callerPhone, $clinicPhone->phone_number, $responseBody);
            $msgId = $smsPayload['_id'];
            unset($smsPayload['_id']);

            $this->line("  ID: {$msgId}");
            $this->line("  Body: {$responseBody}");

            try {
                match ($provider) {
                    'twilio' => $twilioSms->execute($smsPayload),
                    'vonage' => $vonageSms->execute($smsPayload),
                    'messagebird' => $messageBirdSms->execute($smsPayload),
                };
                $this->info('  ✅ Response processed successfully');

                $lead->refresh();
                $this->info("  📊 Lead Stage: {$lead->stage->value}");

                $messageCount = $lead->conversation->messages()->count();
                $this->info("  💬 Messages in conversation: {$messageCount}");
            } catch (\Exception $e) {
                $this->error("  ❌ Error: {$e->getMessage()}");
            }
            $this->info('');
        }

        // Summary
        $this->info('─────────────────────────────────────────');
        $this->info('SUMMARY');
        $this->info('─────────────────────────────────────────');

        $lead->refresh();
        $lead->load(['caller', 'conversation.messages', 'missedCall']);

        $this->table(
            ['Property', 'Value'],
            [
                ['Lead ID', $lead->id],
                ['Stage', $lead->stage->value],
                ['Provider', $provider],
                ['Caller Phone', $lead->caller->phone],
                ['Conversation ID', $lead->conversation->id],
                ['Messages', $lead->conversation->messages->count()],
                ['Missed Call ID', $lead->missedCall?->id ?? 'N/A'],
            ]
        );

        $this->info('');
        $this->info("🔗 View in Inbox: /conversations/{$lead->conversation->id}");
        $this->info('');

        return 0;
    }

    private function buildVoicePayload(string $provider, string $callerPhone, string $clinicPhone): array
    {
        return match ($provider) {
            'twilio' => [
                '_id' => $id = 'CA' . Str::random(32),
                'CallSid' => $id,
                'CallStatus' => 'no-answer',
                'From' => $callerPhone,
                'To' => $clinicPhone,
                'Called' => $clinicPhone,
                'Direction' => 'inbound',
            ],
            'vonage' => [
                '_id' => $id = Str::uuid()->toString(),
                'uuid' => $id,
                'status' => 'unanswered',
                'from' => ltrim($callerPhone, '+'),
                'to' => ltrim($clinicPhone, '+'),
                'direction' => 'inbound',
            ],
            'messagebird' => [
                '_id' => $id = Str::uuid()->toString(),
                'id' => $id,
                'status' => 'no-answer',
                'source' => ltrim($callerPhone, '+'),
                'destination' => ltrim($clinicPhone, '+'),
            ],
        };
    }

    private function buildSmsPayload(string $provider, string $callerPhone, string $clinicPhone, string $body): array
    {
        return match ($provider) {
            'twilio' => [
                '_id' => $id = 'SM' . Str::random(32),
                'MessageSid' => $id,
                'From' => $callerPhone,
                'To' => $clinicPhone,
                'Body' => $body,
            ],
            'vonage' => [
                '_id' => $id = Str::uuid()->toString(),
                'messageId' => $id,
                'msisdn' => ltrim($callerPhone, '+'),
                'to' => ltrim($clinicPhone, '+'),
                'text' => $body,
            ],
            'messagebird' => [
                '_id' => $id = Str::uuid()->toString(),
                'id' => $id,
                'originator' => ltrim($callerPhone, '+'),
                'recipient' => ltrim($clinicPhone, '+'),
                'body' => $body,
            ],
        };
    }

    private function generateRandomPhone(): string
    {
        $areaCode = rand(200, 999);
        $prefix = rand(200, 999);
        $line = rand(1000, 9999);

        return "+1{$areaCode}{$prefix}{$line}";
    }
}
