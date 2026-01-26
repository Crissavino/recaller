<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\ClinicPhoneNumber;
use App\Models\PhoneNumber;
use InvalidArgumentException;

class SmsProviderFactory
{
    public function __construct(
        private TwilioService $twilioService,
        private VonageService $vonageService,
        private MessageBirdService $messageBirdService,
    ) {}

    /**
     * Get the SMS provider service for a given provider name
     */
    public function make(string $provider): SmsProviderInterface
    {
        return match ($provider) {
            'twilio' => $this->twilioService,
            'vonage' => $this->vonageService,
            'messagebird' => $this->messageBirdService,
            default => throw new InvalidArgumentException("Unknown SMS provider: {$provider}"),
        };
    }

    /**
     * Get the SMS provider service for a phone number
     */
    public function forPhoneNumber(PhoneNumber|ClinicPhoneNumber $phoneNumber): SmsProviderInterface
    {
        return $this->make($phoneNumber->provider ?? 'twilio');
    }

    /**
     * Get all available provider names
     */
    public function availableProviders(): array
    {
        return ['twilio', 'vonage', 'messagebird'];
    }
}
