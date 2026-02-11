<?php

namespace App\UseCases\Messaging;

use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Models\ClinicPhoneNumber;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\SmsProviderFactory;
use App\Services\TwilioService;

class SendReplyMessage
{
    public function __construct(
        private SmsProviderFactory $smsProviderFactory,
        private TwilioService $twilioService,
    ) {}

    public function execute(
        int $conversationId,
        string $body,
        int $sentByUserId,
        ?MessageChannel $channel = null,
    ): Message {
        $conversation = Conversation::with(['lead.caller', 'lead.missedCall.clinicPhoneNumber'])
            ->findOrFail($conversationId);

        // Use provided channel, or fall back to conversation's channel, or SMS as default
        $messageChannel = $channel ?? $conversation->channel ?? MessageChannel::SMS;

        // Get the appropriate from phone based on channel
        $fromPhone = $this->getFromPhone($conversation, $messageChannel);

        $message = Message::create([
            'clinic_id' => $conversation->clinic_id,
            'conversation_id' => $conversation->id,
            'channel' => $messageChannel,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $fromPhone,
            'to_phone' => $conversation->lead->caller->phone,
            'body' => $body,
            'status' => 'pending',
            'sent_by_user_id' => $sentByUserId,
        ]);

        // Send via appropriate channel
        $this->sendMessage($message, $messageChannel, $conversation);

        $conversation->updateLastMessageTimestamp();
        $conversation->updateLastStaffReplyTimestamp();

        // Update conversation channel if it changed
        if ($conversation->channel !== $messageChannel) {
            $conversation->update(['channel' => $messageChannel]);
        }

        return $message;
    }

    private function getFromPhone(Conversation $conversation, MessageChannel $channel): string
    {
        if ($channel === MessageChannel::WHATSAPP) {
            // Get WhatsApp number for this clinic
            $whatsappNumber = ClinicPhoneNumber::where('clinic_id', $conversation->clinic_id)
                ->where('is_active', true)
                ->where('phone_number', 'like', 'whatsapp:%')
                ->first();

            if ($whatsappNumber) {
                // Return just the phone part without whatsapp: prefix for storage
                return str_replace('whatsapp:', '', $whatsappNumber->phone_number);
            }
        }

        // Fall back to missed call phone number or empty
        return $conversation->lead->missedCall?->clinicPhoneNumber?->phone_number ?? '';
    }

    private function sendMessage(Message $message, MessageChannel $channel, Conversation $conversation): void
    {
        if ($channel === MessageChannel::WHATSAPP) {
            $this->twilioService->sendWhatsApp($message);
            return;
        }

        // SMS - use the provider factory
        $clinicPhoneNumber = $conversation->lead->missedCall?->clinicPhoneNumber;
        if ($clinicPhoneNumber) {
            $provider = $this->smsProviderFactory->forPhoneNumber($clinicPhoneNumber);
            $provider->sendSms($message);
        }
    }
}
