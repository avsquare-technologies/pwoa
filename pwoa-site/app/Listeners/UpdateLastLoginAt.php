<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;

class UpdateLastLoginAt
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        
        $user->update([
            'last_login_at' => Carbon::now(),
        ]);
    }
}
