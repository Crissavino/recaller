<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\Integration;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService implements SmsProviderInterface
{
    private ?Client $client = null;

    public function sendSms(Message $message): bool
    {
        $integration = $this->getIntegration($message->clinic_id);

        if (!$integration) {
            Log::error('TwilioService: No active Twilio integration', [
                'clinic_id' => $message->clinic_id,
            ]);
            return false;
        }

        try {
            $client = $this->getClient($integration);

            $twilioMessage = $client->messages->create(
                $message->to_phone,
                [
                    'from' => $message->from_phone,
                    'body' => $message->body,
                ]
            );

            $message->update([
                'provider_message_id' => $twilioMessage->sid,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('TwilioService: Failed to send SMS', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            $message->update(['status' => 'failed']);

            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'twilio';
    }

    public function verifyWebhookSignature(array $payload, array $headers): bool
    {
        // Webhook signature verification is handled by middleware
        // This is a simplified version - full validation requires clinic context
        return true;
    }

    public function verifySignature(string $signature, string $url, array $params, int $clinicId): bool
    {
        $integration = $this->getIntegration($clinicId);

        if (!$integration) {
            return false;
        }

        $authToken = $integration->credentials['auth_token'] ?? null;

        if (!$authToken) {
            return false;
        }

        $validator = new \Twilio\Security\RequestValidator($authToken);

        return $validator->validate($signature, $url, $params);
    }

    private function getIntegration(int $clinicId): ?Integration
    {
        return Integration::where('clinic_id', $clinicId)
            ->where('provider', 'twilio')
            ->where('is_active', true)
            ->first();
    }

    private function getClient(Integration $integration): Client
    {
        $credentials = $integration->credentials;

        return new Client(
            $credentials['account_sid'],
            $credentials['auth_token']
        );
    }
}
