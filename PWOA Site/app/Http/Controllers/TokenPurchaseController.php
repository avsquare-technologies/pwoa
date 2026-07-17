<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Services\PrivatePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenPurchaseController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();
        $amount = $request->validate([
            'usd_amount' => ['required', 'numeric', 'min:1'],
        ])['usd_amount'];

        // 1 USD = 1 WASH (Example rate)
        $tokenAmount = $amount; 

        return $user->checkoutCharge($amount * 100, '$WASH Token Purchase', 1, [
            'success_url' => route('token.success') . '?session_id={CHECKOUT_SESSION_ID}&amount=' . $tokenAmount,
            'cancel_url' => route('token.cancel'),
            'metadata' => [
                'user_id' => $user->id,
                'token_amount' => $tokenAmount,
            ],
        ]);
    }

    public function success(Request $request, \App\Actions\Wallet\IssueWashTokensAction $action)
    {
        $user = $request->user();
        $tokenAmount = (float) $request->query('amount');
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            try {
                $session = $user->stripe()->checkout->sessions->retrieve($sessionId);
                
                // Record the payment
                \App\Models\Payment::create([
                    'user_id' => $user->id,
                    'stripe_payment_id' => $session->payment_intent,
                    'amount' => $session->amount_total / 100,
                    'currency' => strtoupper($session->currency),
                    'status' => 'succeeded',
                    'description' => '$WASH Token Purchase',
                    'paid_at' => now(),
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to record Stripe payment: ' . $e->getMessage());
            }
        }
        
        if ($action->execute($user, $tokenAmount)) {
            return redirect()->route('wallet.index')->with('success', "Successfully purchased {$tokenAmount} \$WASH tokens!");
        }

        return redirect()->route('wallet.index')->with('error', 'Payment successful, but token issuance failed. Please contact support.');
    }

    public function cancel()
    {
        return redirect()->route('wallet.index')->with('info', 'Token purchase cancelled.');
    }
}
