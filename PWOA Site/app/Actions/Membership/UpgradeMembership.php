<?php

namespace App\Actions\Membership;

use App\Models\User;
use Exception;

class UpgradeMembership
{
    public function execute(User $user)
    {
        $subscription = $user->subscription('default');

        if (!$subscription) {
            throw new Exception("You must have an active membership subscription to upgrade.");
        }

        $goldPriceId = config('membership.plans.gold.stripe_price_id');

        if (!$goldPriceId) {
            throw new Exception("Gold membership price ID is not configured.");
        }

        if ($subscription->hasPrice($goldPriceId)) {
            throw new Exception("You are already on the Gold Membership tier.");
        }

        // Swap the price in Stripe (prorated by default)
        $subscription->swap($goldPriceId);

        // Sync local status and database
        app(SyncMembershipStatus::class)->execute($user, 'gold');

        return $user;
    }
}
