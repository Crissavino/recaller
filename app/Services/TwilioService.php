<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService implements SmsProviderInterface
{
    private ?Client $client = null;

    public function __construct()
    {
        $accountSid = config('services.twilio.sid');
        $authToken = config('services.twilio.token');

        if ($accountSid && $authToken) {
            $this->client = new Client($accountSid, $authToken);
        }
    }

    public function sendSms(Message $message): bool
    {
        if (!$this->client) {
            Log::error('TwilioService: Twilio credentials not configured');
            return false;
        }

        try {
            $twilioMessage = $this->client->messages->create(
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
        return true;
    }

    public function verifySignature(string $signature, string $url, array $params): bool
    {
        $authToken = config('services.twilio.token');

        if (!$authToken) {
            return false;
        }

        $validator = new \Twilio\Security\RequestValidator($authToken);

        return $validator->validate($signature, $url, $params);
    }

    /**
     * Purchase a phone number from Twilio.
     */
    public function purchasePhoneNumber(string $countryCode = 'US', ?string $areaCode = null): ?array
    {
        if (!$this->client) {
            Log::error('TwilioService: Twilio credentials not configured');
            return null;
        }

        try {
            // Search for available numbers
            $options = [
                'voiceEnabled' => true,
                'smsEnabled' => true,
            ];

            if ($areaCode) {
                $options['areaCode'] = $areaCode;
            }

            $availableNumbers = $this->client->availablePhoneNumbers($countryCode)
                ->local
                ->read($options, 1);

            if (empty($availableNumbers)) {
                Log::warning('TwilioService: No available phone numbers found', [
                    'country' => $countryCode,
                    'area_code' => $areaCode,
                ]);
                return null;
            }

            // Purchase the first available number
            $number = $availableNumbers[0];
            $purchased = $this->client->incomingPhoneNumbers->create([
                'phoneNumber' => $number->phoneNumber,
                'voiceUrl' => config('services.twilio.voice_webhook_url'),
                'smsUrl' => config('services.twilio.sms_webhook_url'),
            ]);

            return [
                'sid' => $purchased->sid,
                'phone_number' => $purchased->phoneNumber,
                'friendly_name' => $purchased->friendlyName,
                'voice_enabled' => true,
                'sms_enabled' => true,
            ];
        } catch (\Exception $e) {
            Log::error('TwilioService: Failed to purchase phone number', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Update webhook URLs for a phone number.
     */
    public function updatePhoneNumberWebhooks(string $sid, string $voiceUrl, string $smsUrl): bool
    {
        if (!$this->client) {
            return false;
        }

        try {
            $this->client->incomingPhoneNumbers($sid)->update([
                'voiceUrl' => $voiceUrl,
                'smsUrl' => $smsUrl,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('TwilioService: Failed to update phone number webhooks', [
                'sid' => $sid,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Release a phone number.
     */
    public function releasePhoneNumber(string $sid): bool
    {
        if (!$this->client) {
            return false;
        }

        try {
            $this->client->incomingPhoneNumbers($sid)->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('TwilioService: Failed to release phone number', [
                'sid' => $sid,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
