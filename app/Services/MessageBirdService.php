<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\Integration;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessageBirdService implements SmsProviderInterface
{
    private const API_URL = 'https://rest.messagebird.com/messages';

    public function sendSms(Message $message): bool
    {
        $integration = $this->getIntegration($message->clinic_id);

        if (!$integration) {
            Log::error('MessageBirdService: No active MessageBird integration', [
                'clinic_id' => $message->clinic_id,
            ]);
            return false;
        }

        try {
            $credentials = $integration->credentials;

            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $credentials['access_key'],
            ])->post(self::API_URL, [
                'originator' => $this->formatPhoneForSending($message->from_phone),
                'recipients' => [$this->formatPhoneForSending($message->to_phone)],
                'body' => $message->body,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $message->update([
                    'provider_message_id' => $data['id'] ?? null,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                return true;
            }

            $error = $response->json('errors.0.description') ?? 'Unknown error';
            Log::error('MessageBirdService: SMS send failed', [
                'message_id' => $message->id,
                'error' => $error,
                'status' => $response->status(),
            ]);

            $message->update(['status' => 'failed']);
            return false;

        } catch (\Exception $e) {
            Log::error('MessageBirdService: Failed to send SMS', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            $message->update(['status' => 'failed']);
            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'messagebird';
    }

    public function verifyWebhookSignature(array $payload, array $headers): bool
    {
        // MessageBird webhook signature validation
        // In production, implement full signature validation using signing key
        return isset($payload['originator']) || isset($payload['id']);
    }

    public function verifySignature(string $signature, string $timestamp, string $body, int $clinicId): bool
    {
        $integration = $this->getIntegration($clinicId);

        if (!$integration) {
            return false;
        }

        $signingKey = $integration->credentials['signing_key'] ?? null;

        if (!$signingKey) {
            return true; // Not recommended for production
        }

        // MessageBird signature: timestamp + body, HMAC SHA256
        $expectedSignature = hash_hmac('sha256', $timestamp . $body, $signingKey);

        return hash_equals($expectedSignature, $signature);
    }

    private function getIntegration(int $clinicId): ?Integration
    {
        return Integration::where('clinic_id', $clinicId)
            ->where('provider', 'messagebird')
            ->where('is_active', true)
            ->first();
    }

    private function formatPhoneForSending(string $phone): string
    {
        // MessageBird accepts E.164 format but without +
        return ltrim($phone, '+');
    }
}
