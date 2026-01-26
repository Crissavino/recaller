<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Lead $lead
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clinic = $this->lead->clinic;
        $caller = $this->lead->caller;
        $phone = $this->formatPhone($caller->phone);

        return (new MailMessage)
            ->subject(__('notifications.new_lead_subject', ['clinic' => $clinic->name]))
            ->greeting(__('notifications.new_lead_greeting'))
            ->line(__('notifications.new_lead_line1', ['phone' => $phone]))
            ->line(__('notifications.new_lead_line2'))
            ->action(__('notifications.view_lead'), route('inbox.index'))
            ->line(__('notifications.new_lead_line3'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'caller_phone' => $this->lead->caller->phone,
            'clinic_id' => $this->lead->clinic_id,
        ];
    }

    private function formatPhone(string $phone): string
    {
        // Format: +1234567890 -> (123) 456-7890
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
}
