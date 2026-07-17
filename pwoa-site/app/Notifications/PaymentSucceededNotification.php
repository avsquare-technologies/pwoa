<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSucceededNotification extends Notification
{
    use Queueable;

    protected $amount;

    protected $currency;

    public function __construct($amount, $currency = 'USD')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Received - PWOA')
            ->line('We have successfully received your payment of '.$this->amount.' '.$this->currency.'.')
            ->line('Thank you for your payment!')
            ->action('View Payment History', url('/payments/history'))
            ->line('If you have any questions, please contact support.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'message' => 'Payment of '.$this->amount.' '.$this->currency.' was successful.',
        ];
    }
}
