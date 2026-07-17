<?php

namespace App\Actions\Membership;

use App\Models\User;
use Exception;

class DowngradeMembership
{
    public function execute(User $user)
    {
        $subscription = $user->subscription('default');

        if (!$subscription) {
            throw new Exception("You must have an active membership subscription to downgrade.");
        }

        $standardPriceId = config('membership.plans.standard.stripe_price_id') ?: env('PWOA_MEMBERSHIP_PRICE_ID');

        if (!$standardPriceId) {
            throw new Exception("Standard membership price ID is not configured.");
        }

        if ($subscription->hasPrice($standardPriceId)) {
            throw new Exception("You are already on the Standard Membership tier.");
        }

        // Swap the price in Stripe without refund proration credit, to apply standard pricing on next cycle
        $subscription->noProrate()->swap($standardPriceId);

        // Sync local status and database
        app(SyncMembershipStatus::class)->execute($user, 'standard');

        return $user;
    }
}
