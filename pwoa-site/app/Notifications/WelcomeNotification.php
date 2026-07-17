<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to PWOA!')
            ->line('Thank you for joining the platform.')
            ->action('Go to Dashboard', url('/dashboard'))
            ->line('We are excited to have you on board!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Welcome to the platform!',
            'action_url' => '/dashboard',
        ];
    }
}
