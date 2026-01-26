<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.welcome_subject'))
            ->greeting(__('notifications.welcome_greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.welcome_line1'))
            ->line(__('notifications.welcome_line2'))
            ->action(__('notifications.welcome_action'), route('setup.index'))
            ->line(__('notifications.welcome_line3'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
        ];
    }
}
