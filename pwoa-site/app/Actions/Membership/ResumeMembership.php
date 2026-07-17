<?php

namespace App\Actions\Membership;

use App\Events\MembershipResumed;
use App\Models\User;

class ResumeMembership
{
    public function execute(User $user)
    {
        if ($user->subscription('default')?->onGracePeriod()) {
            $user->subscription('default')->resume();

            // Sync local status
            app(SyncMembershipStatus::class)->execute($user);

            event(new MembershipResumed($user));
        }

        return $user;
    }
}
