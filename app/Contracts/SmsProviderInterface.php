<?php

namespace App\Contracts;

use App\Models\Message;

interface SmsProviderInterface
{
    /**
     * Send an SMS message
     */
    public function sendSms(Message $message): bool;

    /**
     * Get the provider name identifier
     */
    public function getProviderName(): string;

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(array $payload, array $headers): bool;
}
