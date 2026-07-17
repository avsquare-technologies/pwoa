<?php

namespace App\Listeners;

use App\Actions\Membership\SyncMembershipStatus;
use App\Actions\Payment\RecordPayment;
use App\Events\MembershipActivated;
use App\Events\PaymentReceived;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    public function handle(WebhookReceived $event)
    {
        $payload = $event->payload;
        $type = $payload['type'] ?? '';

        Log::info('Stripe Webhook Received: '.$type);

        if ($type === 'invoice.payment_succeeded') {
            $this->handlePaymentSucceeded($payload['data']['object']);
        } elseif (in_array($type, ['customer.subscription.updated', 'customer.subscription.deleted'])) {
            $this->handleSubscriptionChanged($payload['data']['object']);
        }
    }

    protected function handlePaymentSucceeded(array $invoice)
    {
        $stripeId = $invoice['customer'] ?? null;
        if (! $stripeId) {
            return;
        }

        $user = User::where('stripe_id', $stripeId)->first();
        if (! $user) {
            return;
        }

        $amount = ($invoice['amount_paid'] ?? 0) / 100;

        $payment = app(RecordPayment::class)->execute($user, [
            'stripe_payment_id' => $invoice['payment_intent'] ?? $invoice['charge'] ?? null,
            'stripe_invoice_id' => $invoice['id'] ?? null,
            'amount' => $amount,
            'currency' => strtoupper($invoice['currency'] ?? 'USD'),
            'status' => 'succeeded',
            'description' => 'Invoice payment',
            'receipt_url' => $invoice['hosted_invoice_url'] ?? null,
            'paid_at' => Carbon::createFromTimestamp($invoice['created']),
        ]);

        // Also sync membership
        app(SyncMembershipStatus::class)->execute($user);

        // Dispatch events
        event(new PaymentReceived($user, $payment));
        event(new MembershipActivated($user));
    }

    protected function handleSubscriptionChanged(array $subscription)
    {
        $stripeId = $subscription['customer'] ?? null;
        if (! $stripeId) {
            return;
        }

        $user = User::where('stripe_id', $stripeId)->first();
        if (! $user) {
            return;
        }

        app(SyncMembershipStatus::class)->execute($user);
    }
}
