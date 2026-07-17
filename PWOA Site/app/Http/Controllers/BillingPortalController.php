<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingPortalController extends Controller
{
    /**
     * Redirect the user to Stripe's Customer Billing Portal.
     */
    public function __invoke(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        return $user->redirectToBillingPortal(route('membership.status'));
    }
}
