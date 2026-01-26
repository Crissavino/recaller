<?php

namespace App\UseCases\Messaging;

use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\SmsProviderFactory;

class SendReplyMessage
{
    public function __construct(
        private SmsProviderFactory $smsProviderFactory,
    ) {}

    public function execute(
        int $conversationId,
        string $body,
        int $sentByUserId,
    ): Message {
        $conversation = Conversation::with(['lead.caller', 'lead.missedCall.clinicPhoneNumber'])
            ->findOrFail($conversationId);

        $clinicPhoneNumber = $conversation->lead->missedCall?->clinicPhoneNumber;

        $message = Message::create([
            'clinic_id' => $conversation->clinic_id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $clinicPhoneNumber?->phone_number ?? '',
            'to_phone' => $conversation->lead->caller->phone,
            'body' => $body,
            'status' => 'pending',
            'sent_by_user_id' => $sentByUserId,
        ]);

        // Use the correct provider based on the phone number's provider
        if ($clinicPhoneNumber) {
            $provider = $this->smsProviderFactory->forPhoneNumber($clinicPhoneNumber);
            $provider->sendSms($message);
        }

        $conversation->updateLastMessageTimestamp();
        $conversation->updateLastStaffReplyTimestamp();

        return $message;
    }
}
