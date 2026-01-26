<?php

namespace App\UseCases\Webhooks;

use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Leads\CreateLeadFromMissedCall;
use Illuminate\Support\Facades\Log;

class ProcessMessageBirdVoiceWebhook
{
    public function __construct(
        private CreateLeadFromMissedCall $createLeadFromMissedCall,
    ) {}

    public function execute(array $payload): void
    {
        // MessageBird uses 'id' or 'callId' as unique call identifier
        $callId = $payload['id'] ?? $payload['callId'] ?? null;

        if (!$callId) {
            Log::warning('MessageBird voice webhook missing id', $payload);
            return;
        }

        if ($this->alreadyProcessed($callId)) {
            return;
        }

        // MessageBird sends 'destination' as the called number
        $calledNumber = $payload['destination'] ?? $payload['to'] ?? null;
        $clinicPhoneNumber = $this->findClinicPhoneNumber($calledNumber);

        if (!$clinicPhoneNumber) {
            Log::warning('MessageBird voice webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $callId, $payload);

        if ($this->isMissedCall($payload)) {
            $this->createLeadFromMissedCall->execute(
                clinicId: $clinicPhoneNumber->clinic_id,
                clinicPhoneNumberId: $clinicPhoneNumber->id,
                callerPhone: $this->normalizePhone($payload['source'] ?? $payload['from'] ?? ''),
                providerCallId: $callId,
                ringDurationSeconds: $this->calculateRingDuration($payload),
            );
        }

        $webhookEvent->markAsProcessed();
    }

    private function alreadyProcessed(string $callId): bool
    {
        return WebhookEvent::where('provider', 'messagebird')
            ->where('provider_event_id', $callId)
            ->where('is_processed', true)
            ->exists();
    }

    private function findClinicPhoneNumber(?string $phoneNumber): ?ClinicPhoneNumber
    {
        if (!$phoneNumber) {
            return null;
        }

        $normalized = '+' . ltrim($phoneNumber, '+');

        return ClinicPhoneNumber::where('phone_number', $normalized)
            ->where('provider', 'messagebird')
            ->where('is_active', true)
            ->first();
    }

    private function storeWebhookEvent(int $clinicId, string $callId, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'messagebird',
            'provider_event_id' => $callId,
            'event_type' => 'voice',
            'payload' => $payload,
        ]);
    }

    private function isMissedCall(array $payload): bool
    {
        $status = $payload['status'] ?? '';

        // MessageBird call statuses: no-answer, busy, failed
        return in_array($status, ['no-answer', 'busy', 'failed', 'cancelled']);
    }

    private function calculateRingDuration(array $payload): ?int
    {
        $duration = $payload['duration'] ?? null;

        return $duration ? (int) $duration : null;
    }

    private function normalizePhone(string $phone): string
    {
        if (!str_starts_with($phone, '+')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
