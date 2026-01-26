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
            return;
        }

        $clinicPhoneNumber = $this->findClinicPhoneNumber($payload['Called'] ?? null);

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

        return ClinicPhoneNumber::where('phone_number', $phoneNumber)
            ->where('is_active', true)
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
        $callStatus = $payload['CallStatus'] ?? '';

        return in_array($callStatus, ['no-answer', 'busy', 'canceled']);
    }

    private function calculateRingDuration(array $payload): ?int
    {
        // TODO: Calculate from Twilio timestamps if available
        return null;
    }
}
