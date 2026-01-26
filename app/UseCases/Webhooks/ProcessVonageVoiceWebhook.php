<?php

namespace App\UseCases\Webhooks;

use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Leads\CreateLeadFromMissedCall;
use Illuminate\Support\Facades\Log;

class ProcessVonageVoiceWebhook
{
    public function __construct(
        private CreateLeadFromMissedCall $createLeadFromMissedCall,
    ) {}

    public function execute(array $payload): void
    {
        // Vonage voice webhook uses 'uuid' as unique call identifier
        $callUuid = $payload['uuid'] ?? $payload['conversation_uuid'] ?? null;

        if (!$callUuid) {
            Log::warning('Vonage voice webhook missing uuid', $payload);
            return;
        }

        if ($this->alreadyProcessed($callUuid)) {
            return;
        }

        // Vonage sends 'to' as the called number
        $calledNumber = $payload['to'] ?? null;
        $clinicPhoneNumber = $this->findClinicPhoneNumber($calledNumber);

        if (!$clinicPhoneNumber) {
            Log::warning('Vonage voice webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $callUuid, $payload);

        if ($this->isMissedCall($payload)) {
            $this->createLeadFromMissedCall->execute(
                clinicId: $clinicPhoneNumber->clinic_id,
                clinicPhoneNumberId: $clinicPhoneNumber->id,
                callerPhone: $this->normalizePhone($payload['from'] ?? ''),
                providerCallId: $callUuid,
                ringDurationSeconds: $this->calculateRingDuration($payload),
            );
        }

        $webhookEvent->markAsProcessed();
    }

    private function alreadyProcessed(string $callUuid): bool
    {
        return WebhookEvent::where('provider', 'vonage')
            ->where('provider_event_id', $callUuid)
            ->where('is_processed', true)
            ->exists();
    }

    private function findClinicPhoneNumber(?string $phoneNumber): ?ClinicPhoneNumber
    {
        if (!$phoneNumber) {
            return null;
        }

        // Vonage sends numbers without + prefix
        $normalized = '+' . ltrim($phoneNumber, '+');

        return ClinicPhoneNumber::where('phone_number', $normalized)
            ->where('provider', 'vonage')
            ->where('is_active', true)
            ->first();
    }

    private function storeWebhookEvent(int $clinicId, string $callUuid, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'vonage',
            'provider_event_id' => $callUuid,
            'event_type' => 'voice',
            'payload' => $payload,
        ]);
    }

    private function isMissedCall(array $payload): bool
    {
        $status = $payload['status'] ?? '';

        // Vonage call statuses: unanswered, busy, rejected, timeout, cancelled
        return in_array($status, ['unanswered', 'busy', 'rejected', 'timeout', 'cancelled']);
    }

    private function calculateRingDuration(array $payload): ?int
    {
        $duration = $payload['duration'] ?? null;

        return $duration ? (int) $duration : null;
    }

    private function normalizePhone(string $phone): string
    {
        // Vonage sends numbers without + prefix
        if (!str_starts_with($phone, '+')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
