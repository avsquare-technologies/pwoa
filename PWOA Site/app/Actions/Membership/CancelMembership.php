<?php

namespace App\Actions\Membership;

use App\Events\MembershipCancelled;
use App\Models\User;

class CancelMembership
{
    public function execute(User $user)
    {
        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();

            // Sync local status
            app(SyncMembershipStatus::class)->execute($user);

            event(new MembershipCancelled($user));
        }

        return $user;
    }
}
