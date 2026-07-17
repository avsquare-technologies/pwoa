<?php

namespace App\Livewire;

use App\Services\Shared\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PaymentHistory extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $payments = app(PaymentService::class)->getPaymentsForUser(Auth::user());

        return view('livewire.payment-history', ['payments' => $payments]);
    }
}
