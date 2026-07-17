<?php

namespace App\Actions\Membership;

use App\Models\User;

class SubscribeUser
{
    public function execute(User $user, string $paymentMethodId, string $plan = 'standard')
    {
        $priceId = config("membership.plans.{$plan}.stripe_price_id");
        if (!$priceId) {
            $priceId = config('membership.plans.standard.stripe_price_id') ?: env('PWOA_MEMBERSHIP_PRICE_ID');
        }

        $user->newSubscription('default', $priceId)->create($paymentMethodId);

        // Sync local status
        app(SyncMembershipStatus::class)->execute($user, $plan);

        return $user;
    }
}
