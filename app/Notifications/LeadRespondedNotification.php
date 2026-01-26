<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadRespondedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
        public Conversation $conversation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lead = $this->conversation->lead;
        $clinic = $lead->clinic;
        $caller = $lead->caller;
        $phone = $this->formatPhone($caller->phone);
        $messagePreview = $this->truncateMessage($this->message->body, 100);

        return (new MailMessage)
            ->subject(__('notifications.lead_responded_subject', ['phone' => $phone]))
            ->greeting(__('notifications.lead_responded_greeting'))
            ->line(__('notifications.lead_responded_line1', ['phone' => $phone]))
            ->line('"' . $messagePreview . '"')
            ->action(__('notifications.reply_now'), route('conversations.show', $this->conversation->id))
            ->line(__('notifications.lead_responded_line2'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'lead_id' => $this->conversation->lead_id,
            'message_preview' => $this->truncateMessage($this->message->body, 50),
        ];
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) === 11 && $digits[0] === '1') {
            $digits = substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            return sprintf('(%s) %s-%s',
                substr($digits, 0, 3),
                substr($digits, 3, 3),
                substr($digits, 6)
            );
        }
        return $phone;
    }

    private function truncateMessage(string $message, int $length): string
    {
        if (strlen($message) <= $length) {
            return $message;
        }
        return substr($message, 0, $length) . '...';
    }
}
