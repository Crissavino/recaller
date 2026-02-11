<?php

namespace App\UseCases\Messaging;

use App\Enums\LeadStage;
use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Models\Caller;
use App\Models\Clinic;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\LeadRespondedNotification;
use Illuminate\Support\Facades\Log;

class ReceiveIncomingMessage
{
    public function execute(
        int $clinicId,
        string $fromPhone,
        string $toPhone,
        string $body,
        ?string $providerMessageId = null,
        MessageChannel $channel = MessageChannel::SMS,
    ): ?Message {
        $caller = Caller::where('clinic_id', $clinicId)
            ->where('phone', $fromPhone)
            ->first();

        if (!$caller) {
            Log::info('ReceiveIncomingMessage: Unknown caller, ignoring', [
                'clinic_id' => $clinicId,
                'from_phone' => $fromPhone,
            ]);
            return null;
        }

        $conversation = $this->findActiveConversation($caller);

        if (!$conversation) {
            Log::info('ReceiveIncomingMessage: No active conversation for caller', [
                'caller_id' => $caller->id,
            ]);
            return null;
        }

        $message = Message::create([
            'clinic_id' => $clinicId,
            'conversation_id' => $conversation->id,
            'channel' => $channel,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $fromPhone,
            'to_phone' => $toPhone,
            'body' => $body,
            'provider_message_id' => $providerMessageId,
            'status' => 'received',
        ]);

        $conversation->updateLastMessageTimestamp();

        // Update conversation channel if different from current message
        if ($conversation->channel !== $channel) {
            $conversation->update(['channel' => $channel]);
        }

        $lead = $conversation->lead;
        if ($lead && $lead->stage === LeadStage::CONTACTED) {
            $lead->transitionTo(LeadStage::RESPONDED);
        }

        $this->notifyClinicUsers($message, $conversation, $clinicId);

        return $message;
    }

    private function notifyClinicUsers(Message $message, Conversation $conversation, int $clinicId): void
    {
        $clinic = Clinic::with('users')->find($clinicId);

        if (!$clinic) {
            return;
        }

        foreach ($clinic->users as $user) {
            if ($user->wantsNotification('email_lead_responded')) {
                $user->notify(new LeadRespondedNotification($message, $conversation));
            }
        }
    }

    private function findActiveConversation(Caller $caller): ?Conversation
    {
        return Conversation::whereHas('lead', function ($query) use ($caller) {
            $query->where('caller_id', $caller->id);
        })
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
