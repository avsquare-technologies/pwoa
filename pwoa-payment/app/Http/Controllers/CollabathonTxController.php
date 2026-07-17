<?php

namespace App\Http\Controllers;

use App\Services\CollabathonService;
use Illuminate\Http\Request;
use Log;

class CollabathonTxController extends Controller
{
    protected $service;

    public function __construct(CollabathonService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle Prize Pool Funding or Winner Payouts
     */
    public function payment(Request $request)
    {
        // Log::info('Processing Collabathon Payment', $request->all());
        $validated = $request->validate([
            'collabathon_id' => 'required|integer',
            // 'type' => 'required|in:FUND,PRIZE,REFUND',
            'type' => 'required|string',
            'source' => 'required|string',
            'destination' => 'required|string',
            'amount' => 'required|numeric|min:0.000001',
        ]);

        $result = $this->service->processPayment(
            $validated['collabathon_id'],
            $validated['type'],
            $validated['source'],
            $validated['destination'],
            $validated['amount']
        );

        return response()->json($result);
    }
    public function payoutPayment(Request $request)
    {
        // Log::info('Processing Collabathon Payout Payment', $request->all());
        $validated = $request->validate([
            'collabathon_id' => 'required|integer',
            // 'type' => 'required|in:FUND,PRIZE,REFUND',
            'type' => 'required|string',
            'source' => 'required|string',
            'destination' => 'required|string',
            'amount' => 'required|numeric|min:0.000001',
        ]);

        $result = $this->service->processPayoutPayment(
            $validated['collabathon_id'],
            $validated['type'],
            $validated['source'],
            $validated['destination'],
            $validated['amount']
        );

        return response()->json($result);
    }

    /**
     * Handle Buying a Ticket
     */
    public function buyTicket(Request $request)
    {
        $validated = $request->validate([
            'collabathon_id' => 'required|integer',
            'seller_address' => 'required|string',
            'buyer_address' => 'required|string',
            'token_id' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        Log::info('Buying Ticket', $validated);

        $result = $this->service->buyTicket(
            $validated['collabathon_id'],
            $validated['seller_address'],
            $validated['buyer_address'],
            $validated['token_id'],
            $validated['amount']
        );

        return response()->json($result);
    }
}
