<?php

namespace App\Actions\Payment;

use App\Models\Payment;

class RefundPayment
{
    public function execute(Payment $payment)
    {
        if ($payment->user && $payment->stripe_payment_id) {
            $payment->user->refund($payment->stripe_payment_id);
            $payment->update(['status' => 'refunded']);
        }

        return $payment;
    }
}
