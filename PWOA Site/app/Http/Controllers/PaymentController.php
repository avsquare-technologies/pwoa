<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PaymentController extends Controller
{
    public function history(): View
    {
        return view('pages.payments.history');
    }
}
