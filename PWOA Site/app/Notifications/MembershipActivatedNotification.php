<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipActivatedNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Membership Activated - PWOA')
            ->line('Your annual membership has been successfully activated.')
            ->line('You now have full access to all platform features.')
            ->action('Visit Dashboard', url('/dashboard'))
            ->line('Thank you for your support!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Membership Activated',
            'message' => 'Your annual membership is now active.',
            'action_url' => '/dashboard',
        ];
    }
}
