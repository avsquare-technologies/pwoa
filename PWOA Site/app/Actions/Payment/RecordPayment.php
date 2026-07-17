<?php

namespace App\Actions\Payment;

use App\Models\User;

class RecordPayment
{
    public function execute(User $user, array $data)
    {
        return $user->payments()->create([
            'stripe_payment_id' => $data['stripe_payment_id'] ?? null,
            'stripe_invoice_id' => $data['stripe_invoice_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'card_last_four' => $data['card_last_four'] ?? null,
            'receipt_url' => $data['receipt_url'] ?? null,
            'paid_at' => $data['paid_at'] ?? now(),
        ]);
    }
}
