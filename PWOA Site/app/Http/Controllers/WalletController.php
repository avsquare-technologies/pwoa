<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Services\PublicWalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected PublicWalletService $walletService,
        protected \App\Services\XamanService $xamanService,
        protected \App\Services\PrivatePaymentService $privatePaymentService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return redirect()->route('dashboard')->with('error', 'Wallet not found. Please contact support.');
        }

        // Fetch balance from PublicWalletService (XRPL direct via WebSocket)
        $balance = $this->walletService->getBalance($wallet);

        // Fetch transactions from PublicWalletService (XRPL direct via WebSocket)
        $transactions = $this->walletService->fetchTransactions($wallet);

        // dd($balance);

        return view('frontend.wallet.index', [
            'wallet' => $wallet,
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }

    public function createXamanLogin()
    {
        try {
            $payload = $this->xamanService->createLoginPayload();
            return response()->json([
                'uuid' => $payload->uuid,
                'qr_png' => $payload->refs->qrPng,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create login request.'], 500);
        }
    }

    public function checkXamanLogin($uuid)
    {
        try {
            $payload = $this->xamanService->getPayload($uuid);
            return response()->json([
                'signed' => $payload->payloadMeta->signed ?? false,
                'account' => $payload->response->account ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['signed' => false, 'message' => 'Status check failed.'], 500);
        }
    }

    public function checkXamanAccess(Request $request)
    {
        $request->validate(['address' => 'required|string']);
        $result = $this->xamanService->checkAndPrepareAccess($request->address);
        return response()->json($result);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'destination' => 'required|string',
            'amount' => 'required|numeric|min:0.000001',
        ]);

        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found.'], 404);
        }

        // Call the private service for the actual transfer
        $result = $this->privatePaymentService->sendExternal(
            $wallet->address,
            $request->destination,
            $request->amount
        );

        if ($result && isset($result['success']) && $result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Transfer submitted to ledger!',
                'tx_hash' => $result['tx_hash'] ?? ($result['data']['tx_hash'] ?? null),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Transfer failed.',
        ], 400);
    }
}
