<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessageBirdService implements SmsProviderInterface
{
    private const API_URL = 'https://rest.messagebird.com';

    private ?string $accessKey = null;
    private ?string $signingKey = null;

    public function __construct()
    {
        $this->accessKey = config('services.messagebird.access_key');
        $this->signingKey = config('services.messagebird.signing_key');
    }

    public function sendSms(Message $message): bool
    {
        if (!$this->accessKey) {
            Log::error('MessageBirdService: Access key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->accessKey,
                'Content-Type' => 'application/json',
            ])->post(self::API_URL . '/messages', [
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
                'response' => $response->json(),
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
        // Basic validation if no signing key configured
        if (!$this->signingKey) {
            return isset($payload['originator']) || isset($payload['id']) || isset($payload['source']);
        }

        return true;
    }

    /**
     * Verify MessageBird webhook signature
     *
     * MessageBird uses HMAC-SHA256 signature: hash_hmac('sha256', timestamp + body, signing_key)
     */
    public function verifySignature(string $signature, string $timestamp, string $body): bool
    {
        if (!$this->signingKey) {
            Log::warning('MessageBirdService: No signing key configured, skipping signature verification');
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $timestamp . $body, $this->signingKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * List available phone numbers from MessageBird
     */
    public function listAvailableNumbers(string $countryCode = 'ES', array $features = ['sms', 'voice']): ?array
    {
        if (!$this->accessKey) {
            Log::error('MessageBirdService: Access key not configured');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->accessKey,
            ])->get(self::API_URL . '/available-phone-numbers/' . $countryCode, [
                'features' => implode(',', $features),
                'limit' => 10,
            ]);

            if ($response->successful()) {
                return $response->json('items') ?? [];
            }

            Log::error('MessageBirdService: Failed to list available numbers', [
                'country' => $countryCode,
                'error' => $response->json(),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('MessageBirdService: Failed to list available numbers', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Purchase a phone number from MessageBird
     */
    public function purchasePhoneNumber(string $number): ?array
    {
        if (!$this->accessKey) {
            Log::error('MessageBirdService: Access key not configured');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->accessKey,
                'Content-Type' => 'application/json',
            ])->post(self::API_URL . '/phone-numbers', [
                'number' => $number,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id' => $data['id'] ?? null,
                    'phone_number' => $data['number'] ?? $number,
                    'country' => $data['country'] ?? null,
                    'features' => $data['features'] ?? [],
                ];
            }

            Log::error('MessageBirdService: Failed to purchase phone number', [
                'number' => $number,
                'error' => $response->json(),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('MessageBirdService: Failed to purchase phone number', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get owned phone numbers
     */
    public function listOwnedNumbers(): ?array
    {
        if (!$this->accessKey) {
            Log::error('MessageBirdService: Access key not configured');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->accessKey,
            ])->get(self::API_URL . '/phone-numbers');

            if ($response->successful()) {
                return $response->json('items') ?? [];
            }

            Log::error('MessageBirdService: Failed to list owned numbers', [
                'error' => $response->json(),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('MessageBirdService: Failed to list owned numbers', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Configure webhooks for a phone number (Flow Builder required)
     */
    public function configureNumberWebhooks(string $numberId, string $voiceUrl, string $smsUrl): bool
    {
        // MessageBird uses Flow Builder for webhook configuration
        // This requires creating a "Flow" that handles incoming calls/SMS
        // For now, webhooks need to be configured manually in the MessageBird dashboard
        Log::info('MessageBirdService: Webhook configuration requires Flow Builder setup', [
            'number_id' => $numberId,
            'voice_url' => $voiceUrl,
            'sms_url' => $smsUrl,
        ]);

        return true;
    }

    /**
     * Release a phone number
     */
    public function releasePhoneNumber(string $numberId): bool
    {
        if (!$this->accessKey) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->accessKey,
            ])->delete(self::API_URL . '/phone-numbers/' . $numberId);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('MessageBirdService: Failed to release phone number', [
                'number_id' => $numberId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function formatPhoneForSending(string $phone): string
    {
        // MessageBird accepts E.164 format but without +
        return ltrim($phone, '+');
    }
}
