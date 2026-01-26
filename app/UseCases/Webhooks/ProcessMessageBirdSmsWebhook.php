<?php

namespace App\UseCases\Webhooks;

use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Messaging\ReceiveIncomingMessage;
use Illuminate\Support\Facades\Log;

class ProcessMessageBirdSmsWebhook
{
    public function __construct(
        private ReceiveIncomingMessage $receiveIncomingMessage,
    ) {}

    public function execute(array $payload): void
    {
        // MessageBird uses 'id' as unique message identifier
        $messageId = $payload['id'] ?? null;

        if (!$messageId) {
            Log::warning('MessageBird SMS webhook missing id', $payload);
            return;
        }

        if ($this->alreadyProcessed($messageId)) {
            return;
        }

        // MessageBird sends 'recipient' as the destination number (our number)
        $toNumber = $payload['recipient'] ?? $payload['to'] ?? null;
        $clinicPhoneNumber = $this->findClinicPhoneNumber($toNumber);

        if (!$clinicPhoneNumber) {
            Log::warning('MessageBird SMS webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $messageId, $payload);

        // MessageBird uses 'originator' for sender, 'body' for content
        $fromPhone = $this->normalizePhone($payload['originator'] ?? $payload['from'] ?? '');
        $body = $payload['body'] ?? $payload['message'] ?? '';

        if ($fromPhone && $body) {
            $this->receiveIncomingMessage->execute(
                clinicId: $clinicPhoneNumber->clinic_id,
                fromPhone: $fromPhone,
                toPhone: $clinicPhoneNumber->phone_number,
                body: $body,
                providerMessageId: $messageId,
            );
        }

        $webhookEvent->markAsProcessed();
    }

    private function alreadyProcessed(string $messageId): bool
    {
        return WebhookEvent::where('provider', 'messagebird')
            ->where('provider_event_id', $messageId)
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

    private function storeWebhookEvent(int $clinicId, string $messageId, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'messagebird',
            'provider_event_id' => $messageId,
            'event_type' => 'sms',
            'payload' => $payload,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        if (!str_starts_with($phone, '+')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
