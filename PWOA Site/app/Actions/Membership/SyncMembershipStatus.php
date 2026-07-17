<?php

namespace App\Actions\Membership;

use App\Models\User;

class SyncMembershipStatus
{
    public function execute(User $user, string $plan = 'standard')
    {
        $subscription = $user->subscription('default');

        $isActive = $subscription ? $subscription->active() : false;

        // Auto-detect plan from Stripe subscription price id
        if ($isActive && $subscription) {
            $goldPrice = config('membership.plans.gold.stripe_price_id');
            if ($goldPrice && $subscription->hasPrice($goldPrice)) {
                $plan = 'gold';
            }
        }

        $user->membershipStatus()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_active' => $isActive,
                'plan' => $plan,
                'auto_renew' => $subscription && ! $subscription->onGracePeriod(),
                'expires_at' => $subscription?->ends_at ?? ($isActive ? now()->addYear() : null),
                'cancelled_at' => ($subscription && $subscription->canceled()) ? ($subscription->ends_at ?? now()) : null,
            ]
        );

        if ($isActive) {
            $user->businesses()->update(['membership_tier' => $plan]);
        }

        return $user->refresh();
    }
}
