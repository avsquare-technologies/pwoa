<?php

namespace App\Services\Shared;

use App\Models\User;

class PaymentService
{
    public function getPaymentsForUser(User $user)
    {
        return $user->payments()->orderBy('paid_at', 'desc')->paginate(10);
    }
}
