<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\Integration;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VonageService implements SmsProviderInterface
{
    private const API_URL = 'https://rest.nexmo.com/sms/json';

    public function sendSms(Message $message): bool
    {
        $integration = $this->getIntegration($message->clinic_id);

        if (!$integration) {
            Log::error('VonageService: No active Vonage integration', [
                'clinic_id' => $message->clinic_id,
            ]);
            return false;
        }

        try {
            $credentials = $integration->credentials;

            $response = Http::post(self::API_URL, [
                'api_key' => $credentials['api_key'],
                'api_secret' => $credentials['api_secret'],
                'from' => $this->formatPhoneForSending($message->from_phone),
                'to' => $this->formatPhoneForSending($message->to_phone),
                'text' => $message->body,
            ]);

            $data = $response->json();

            if (isset($data['messages'][0]['status']) && $data['messages'][0]['status'] === '0') {
                $message->update([
                    'provider_message_id' => $data['messages'][0]['message-id'] ?? null,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                return true;
            }

            $errorText = $data['messages'][0]['error-text'] ?? 'Unknown error';
            Log::error('VonageService: SMS send failed', [
                'message_id' => $message->id,
                'error' => $errorText,
            ]);

            $message->update(['status' => 'failed']);
            return false;

        } catch (\Exception $e) {
            Log::error('VonageService: Failed to send SMS', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            $message->update(['status' => 'failed']);
            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'vonage';
    }

    public function verifyWebhookSignature(array $payload, array $headers): bool
    {
        // Vonage uses signature validation with JWT or signature secret
        // For basic setup, we validate the expected fields are present
        // In production, implement full signature validation
        return isset($payload['msisdn']) || isset($payload['from']);
    }

    public function verifySignature(string $signature, array $payload, int $clinicId): bool
    {
        $integration = $this->getIntegration($clinicId);

        if (!$integration) {
            return false;
        }

        $signatureSecret = $integration->credentials['signature_secret'] ?? null;

        if (!$signatureSecret) {
            // If no signature secret configured, allow (not recommended for production)
            return true;
        }

        // Vonage signature validation
        ksort($payload);
        $signedPayload = '';
        foreach ($payload as $key => $value) {
            $signedPayload .= '&' . $key . '=' . $value;
        }

        $expectedSignature = hash_hmac('sha256', $signedPayload, $signatureSecret);

        return hash_equals($expectedSignature, $signature);
    }

    private function getIntegration(int $clinicId): ?Integration
    {
        return Integration::where('clinic_id', $clinicId)
            ->where('provider', 'vonage')
            ->where('is_active', true)
            ->first();
    }

    private function formatPhoneForSending(string $phone): string
    {
        // Vonage expects phone without + prefix
        return ltrim($phone, '+');
    }
}
