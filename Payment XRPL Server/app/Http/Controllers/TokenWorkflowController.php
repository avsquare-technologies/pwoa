<?php

namespace App\Http\Controllers;

use App\Services\TokenWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenWorkflowController extends Controller
{
    protected $service;

    public function __construct(TokenWorkflowService $service)
    {
        $this->service = $service;
    }

    public function runFullFlow(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|max:40',
            'amount'   => 'required|numeric|min:0.000001'
        ]);

        $currency = $request->input('currency');
        $amount   = $request->input('amount');

        Log::info("Starting Full Token Flow for $amount $currency");
        // dd($request->all());

        try {
            $step1 = $this->service->configureIssuer();
            $step2 = $this->service->makeTrustLine($currency);
            $step3 = $this->service->sendToken($currency, $amount);
            $step4 = $this->service->confirmBalances($currency);

            return response()->json([
                'status'  => 'success',
                'message' => 'Token issuance cycle completed successfully.',
                'flow'    => [
                    '1_configure_issuer' => $step1,
                    '2_trust_line'       => $step2,
                    '3_minting'          => $step3,
                    '4_verification'     => $step4
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Token Flow Failed: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'trace'   => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function setupIssuer()
    {
        try {
            $result = $this->service->configureIssuer();
            return response()->json(['status' => 'success', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createTrustLine(Request $request)
    {
        $request->validate(['currency' => 'required|string']);

        try {
            $result = $this->service->makeTrustLine($request->input('currency'));
            return response()->json(['status' => 'success', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mintTokens(Request $request)
    {
        $request->validate([
            'currency' => 'required|string',
            'amount'   => 'required|numeric'
        ]);

        try {
            $result = $this->service->sendToken(
                $request->input('currency'),
                $request->input('amount')
            );
            return response()->json(['status' => 'success', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkBalance(Request $request)
    {
        $request->validate(['currency' => 'required|string']);

        try {
            $result = $this->service->confirmBalances($request->input('currency'));
            return response()->json(['status' => 'success', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
