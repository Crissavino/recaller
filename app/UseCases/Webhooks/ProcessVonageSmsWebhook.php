<?php

namespace App\UseCases\Webhooks;

use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Messaging\ReceiveIncomingMessage;
use Illuminate\Support\Facades\Log;

class ProcessVonageSmsWebhook
{
    public function __construct(
        private ReceiveIncomingMessage $receiveIncomingMessage,
    ) {}

    public function execute(array $payload): void
    {
        // Vonage SMS webhook uses 'messageId' or 'message-id' as unique identifier
        $messageId = $payload['messageId'] ?? $payload['message-id'] ?? null;

        if (!$messageId) {
            Log::warning('Vonage SMS webhook missing messageId', $payload);
            return;
        }

        if ($this->alreadyProcessed($messageId)) {
            return;
        }

        // Vonage sends 'to' as the destination number (our number)
        $toNumber = $payload['to'] ?? null;
        $clinicPhoneNumber = $this->findClinicPhoneNumber($toNumber);

        if (!$clinicPhoneNumber) {
            Log::warning('Vonage SMS webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $messageId, $payload);

        // Vonage uses 'msisdn' or 'from' for sender, 'text' for body
        $fromPhone = $this->normalizePhone($payload['msisdn'] ?? $payload['from'] ?? '');
        $body = $payload['text'] ?? $payload['body'] ?? '';

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
        return WebhookEvent::where('provider', 'vonage')
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
            ->where('provider', 'vonage')
            ->where('is_active', true)
            ->first();
    }

    private function storeWebhookEvent(int $clinicId, string $messageId, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'vonage',
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
