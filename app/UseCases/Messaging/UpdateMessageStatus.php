<?php

namespace App\UseCases\Messaging;

use App\Models\Message;
use Illuminate\Support\Facades\Log;

class UpdateMessageStatus
{
    public function execute(string $providerMessageId, string $status): ?Message
    {
        $message = Message::where('provider_message_id', $providerMessageId)->first();

        if (!$message) {
            Log::info('UpdateMessageStatus: Message not found', [
                'provider_message_id' => $providerMessageId,
            ]);
            return null;
        }

        $normalizedStatus = $this->normalizeStatus($status);

        $message->update([
            'status' => $normalizedStatus,
            'delivered_at' => $normalizedStatus === 'delivered' ? now() : $message->delivered_at,
        ]);

        Log::info('UpdateMessageStatus: Status updated', [
            'message_id' => $message->id,
            'status' => $normalizedStatus,
        ]);

        return $message;
    }

    private function normalizeStatus(string $twilioStatus): string
    {
        // Twilio SMS statuses: queued, sending, sent, delivered, undelivered, failed
        return match ($twilioStatus) {
            'queued', 'sending' => 'pending',
            'sent' => 'sent',
            'delivered' => 'delivered',
            'undelivered', 'failed' => 'failed',
            default => $twilioStatus,
        };
    }
}
