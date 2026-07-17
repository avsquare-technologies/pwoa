<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipCancelledNotification extends Notification
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
            ->subject('Membership Cancelled - PWOA')
            ->line('Your membership has been cancelled as per your request.')
            ->line('You will continue to have access until the end of your current billing period.')
            ->action('Manage Membership', url('/membership'))
            ->line('We are sorry to see you go!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Membership Cancelled',
            'message' => 'Your membership has been cancelled but remains active until the end of the period.',
            'action_url' => '/membership',
        ];
    }
}
