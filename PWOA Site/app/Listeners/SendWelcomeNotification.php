<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\WelcomeNotification;

class SendWelcomeNotification
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // $event->user->notify(new WelcomeNotification);
    }
}
