<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Notifications\PaymentSucceededNotification;

class SendPaymentSucceededNotification
{
    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $event->user->notify(new PaymentSucceededNotification($event->payment->amount, $event->payment->currency));
    }
}
