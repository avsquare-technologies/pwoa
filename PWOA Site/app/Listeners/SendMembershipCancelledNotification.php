<?php

namespace App\Listeners;

use App\Events\MembershipCancelled;
use App\Notifications\MembershipCancelledNotification;

class SendMembershipCancelledNotification
{
    /**
     * Handle the event.
     */
    public function handle(MembershipCancelled $event): void
    {
        $event->user->notify(new MembershipCancelledNotification);
    }
}
