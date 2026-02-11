<?php

namespace App\UseCases\Webhooks;

use App\Enums\MessageChannel;
use App\Models\ClinicPhoneNumber;
use App\Models\WebhookEvent;
use App\UseCases\Messaging\ReceiveIncomingMessage;
use Illuminate\Support\Facades\Log;

class ProcessTwilioWhatsAppWebhook
{
    public function __construct(
        private ReceiveIncomingMessage $receiveIncomingMessage,
    ) {}

    public function execute(array $payload): void
    {
        $messageSid = $payload['MessageSid'] ?? $payload['SmsSid'] ?? null;

        if (!$messageSid) {
            Log::warning('Twilio WhatsApp webhook missing MessageSid', $payload);
            return;
        }

        if ($this->alreadyProcessed($messageSid)) {
            return;
        }

        $clinicPhoneNumber = $this->findClinicPhoneNumber($payload['To'] ?? null);

        if (!$clinicPhoneNumber) {
            Log::warning('Twilio WhatsApp webhook for unknown number', $payload);
            return;
        }

        $webhookEvent = $this->storeWebhookEvent($clinicPhoneNumber->clinic_id, $messageSid, $payload);

        // Extract phone number from "whatsapp:+34625941020" format
        $fromPhone = $this->extractPhoneNumber($payload['From'] ?? '');
        $toPhone = $this->extractPhoneNumber($payload['To'] ?? '');

        $this->receiveIncomingMessage->execute(
            clinicId: $clinicPhoneNumber->clinic_id,
            fromPhone: $fromPhone,
            toPhone: $toPhone,
            body: $payload['Body'] ?? '',
            providerMessageId: $messageSid,
            channel: MessageChannel::WHATSAPP,
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

        // WhatsApp numbers come as "whatsapp:+14155238886"
        return ClinicPhoneNumber::where('is_active', true)
            ->where(function ($query) use ($phoneNumber) {
                $query->where('phone_number', $phoneNumber)
                    ->orWhere('phone_number', $this->extractPhoneNumber($phoneNumber));
            })
            ->first();
    }

    private function extractPhoneNumber(string $whatsappNumber): string
    {
        // Convert "whatsapp:+34625941020" to "+34625941020"
        return str_replace('whatsapp:', '', $whatsappNumber);
    }

    private function storeWebhookEvent(int $clinicId, string $messageSid, array $payload): WebhookEvent
    {
        return WebhookEvent::create([
            'clinic_id' => $clinicId,
            'provider' => 'twilio',
            'provider_event_id' => $messageSid,
            'event_type' => 'whatsapp',
            'payload' => $payload,
        ]);
    }
}
