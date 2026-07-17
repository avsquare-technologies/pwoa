<?php

namespace App\Listeners;

use App\Events\MembershipActivated;
use App\Notifications\MembershipActivatedNotification;

class SendMembershipActivatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(MembershipActivated $event): void
    {
        $event->user->notify(new MembershipActivatedNotification);
    }
}
