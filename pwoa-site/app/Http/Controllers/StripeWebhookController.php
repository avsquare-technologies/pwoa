<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Payment;
use App\Actions\Membership\SyncMembershipStatus;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * Handle customer subscription deleted.
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        if ($response->getStatusCode() === 200) {
            $customerId = $payload['data']['object']['customer'];
            $user = User::where('stripe_id', $customerId)->first();

            if ($user) {
                // Sync status locally
                app(SyncMembershipStatus::class)->execute($user);
                
                // Log action
                Log::info("Stripe Webhook: Subscription deleted for User ID {$user->id}");
            }
        }

        return $response;
    }

    /**
     * Handle customer subscription updated.
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);

        if ($response->getStatusCode() === 200) {
            $customerId = $payload['data']['object']['customer'];
            $user = User::where('stripe_id', $customerId)->first();

            if ($user) {
                // Sync status locally
                app(SyncMembershipStatus::class)->execute($user);
                
                Log::info("Stripe Webhook: Subscription updated for User ID {$user->id}");
            }
        }

        return $response;
    }

    /**
     * Handle invoice payment succeeded.
     */
    protected function handleInvoicePaymentSucceeded(array $payload): Response
    {
        $customerId = $payload['data']['object']['customer'];
        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            $invoice = $payload['data']['object'];
            
            // Sync status locally
            app(SyncMembershipStatus::class)->execute($user);

            // Record payment in database
            Payment::create([
                'user_id' => $user->id,
                'stripe_payment_id' => $invoice['payment_intent'] ?? null,
                'stripe_invoice_id' => $invoice['id'] ?? null,
                'amount' => ($invoice['amount_paid'] ?? 0) / 100, // convert from cents
                'currency' => strtoupper($invoice['currency'] ?? 'USD'),
                'status' => 'succeeded',
                'description' => 'Membership subscription payment - Invoice ' . ($invoice['number'] ?? ''),
                'paid_at' => now(),
            ]);

            Log::info("Stripe Webhook: Invoice payment succeeded and logged for User ID {$user->id}");
        }

        return new Response('Webhook Handled', 200);
    }
}
