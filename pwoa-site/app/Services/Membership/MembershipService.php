<?php

namespace App\Services\Membership;

use App\Models\User;
use Carbon\Carbon;

class MembershipService
{
    public function getSubscriptionDetails(User $user): ?array
    {
        $subscription = $user->subscription('default');

        if (! $subscription) {
            return null;
        }

        return [
            'name' => $subscription->type,
            'stripe_id' => $subscription->stripe_id,
            'stripe_status' => $subscription->stripe_status,
            'stripe_price' => $subscription->stripe_price,
            'quantity' => $subscription->quantity,
            'trial_ends_at' => $subscription->trial_ends_at,
            'ends_at' => $subscription->ends_at,
            'on_grace_period' => $subscription->onGracePeriod(),
            'active' => $subscription->active(),
            'canceled' => $subscription->canceled(),
            'incomplete' => $subscription->incomplete(),
            'past_due' => $subscription->pastDue(),
        ];
    }

    public function getDaysUntilExpiry(User $user): ?int
    {
        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->ends_at) {
            return null;
        }

        return (int) Carbon::now()->diffInDays($subscription->ends_at, false);
    }
}
