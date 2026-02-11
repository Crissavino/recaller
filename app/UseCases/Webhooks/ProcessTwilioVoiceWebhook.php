<?php

namespace App\UseCases\Webhooks;

use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Leads\CreateLeadFromMissedCall;
use Illuminate\Support\Facades\Log;

class ProcessTwilioVoiceWebhook
{
    public function __construct(
        private CreateLeadFromMissedCall $createLeadFromMissedCall,
    ) {}

    public function execute(array $payload): void
    {
        $callSid = $payload['CallSid'] ?? null;

        if (!$callSid) {
            Log::warning('Twilio voice webhook missing CallSid', $payload);
            return;
        }

        if ($this->alreadyProcessed($callSid)) {
            Log::info('Twilio voice webhook already processed', ['call_sid' => $callSid]);
            return;
        }

        // Try to find clinic phone number - check both 'Called' and 'To'
        // In forward results, 'Called' is the forward-to number, but 'To' is the Twilio number
        $clinicPhoneNumber = $this->findClinicPhoneNumber($payload['Called'] ?? null);

        if (!$clinicPhoneNumber) {
            $clinicPhoneNumber = $this->findClinicPhoneNumber($payload['To'] ?? null);
        }

        if (!$clinicPhoneNumber) {
            Log::warning('Twilio voice webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $callSid, $payload);

        if ($this->isMissedCall($payload)) {
            $this->createLeadFromMissedCall->execute(
                clinicId: $clinicPhoneNumber->clinic_id,
                clinicPhoneNumberId: $clinicPhoneNumber->id,
                callerPhone: $payload['From'] ?? '',
                providerCallId: $callSid,
                ringDurationSeconds: $this->calculateRingDuration($payload),
            );
        }

        $webhookEvent->markAsProcessed();
    }

    private function alreadyProcessed(string $callSid): bool
    {
        return WebhookEvent::where('provider', 'twilio')
            ->where('provider_event_id', $callSid)
            ->where('is_processed', true)
            ->exists();
    }

    private function findClinicPhoneNumber(?string $phoneNumber): ?ClinicPhoneNumber
    {
        if (!$phoneNumber) {
            return null;
        }

        // Normalize phone number - try with and without +
        $normalized = ltrim($phoneNumber, '+');
        $withPlus = '+' . $normalized;

        return ClinicPhoneNumber::where('is_active', true)
            ->where(function ($query) use ($phoneNumber, $normalized, $withPlus) {
                $query->where('phone_number', $phoneNumber)
                    ->orWhere('phone_number', $normalized)
                    ->orWhere('phone_number', $withPlus);
            })
            ->first();
    }

    private function storeWebhookEvent(int $clinicId, string $callSid, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'twilio',
            'provider_event_id' => $callSid,
            'event_type' => 'voice',
            'payload' => $payload,
        ]);
    }

    private function isMissedCall(array $payload): bool
    {
        // For forward results, check DialCallStatus
        $dialCallStatus = $payload['DialCallStatus'] ?? null;
        if ($dialCallStatus) {
            // If the forwarded call was not answered
            return in_array($dialCallStatus, ['no-answer', 'busy', 'failed', 'canceled']);
        }

        // For direct calls (no forwarding), check CallStatus
        $callStatus = $payload['CallStatus'] ?? '';

        // Include 'ringing' for numbers without forwarding configured
        return in_array($callStatus, ['ringing', 'in-progress', 'no-answer', 'busy', 'canceled']);
    }

    private function calculateRingDuration(array $payload): ?int
    {
        // TODO: Calculate from Twilio timestamps if available
        return null;
    }
}
