<?php

namespace App\UseCases\Webhooks;

use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Messaging\ReceiveIncomingMessage;
use Illuminate\Support\Facades\Log;

class ProcessTwilioSmsWebhook
{
    public function __construct(
        private ReceiveIncomingMessage $receiveIncomingMessage,
    ) {}

    public function execute(array $payload): void
    {
        $messageSid = $payload['MessageSid'] ?? $payload['SmsSid'] ?? null;

        if (!$messageSid) {
            Log::warning('Twilio SMS webhook missing MessageSid', $payload);
            return;
        }

        if ($this->alreadyProcessed($messageSid)) {
            return;
        }

        $clinicPhoneNumber = $this->findClinicPhoneNumber($payload['To'] ?? null);

        if (!$clinicPhoneNumber) {
            Log::warning('Twilio SMS webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $messageSid, $payload);

        $this->receiveIncomingMessage->execute(
            clinicId: $clinicPhoneNumber->clinic_id,
            fromPhone: $payload['From'] ?? '',
            toPhone: $payload['To'] ?? '',
            body: $payload['Body'] ?? '',
            providerMessageId: $messageSid,
        );

        $webhookEvent->markAsProcessed();
    }

    private function alreadyProcessed(string $messageSid): bool
    {
        return WebhookEvent::where('provider', 'twilio')
            ->where('provider_event_id', $messageSid)
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

    private function storeWebhookEvent(int $clinicId, string $messageSid, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'twilio',
            'provider_event_id' => $messageSid,
            'event_type' => 'sms',
            'payload' => $payload,
        ]);
    }
}
