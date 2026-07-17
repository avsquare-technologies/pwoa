<?php

namespace App\Http\Controllers;

use App\Models\OrderTransaction;
use App\Models\Wallet;
use App\Services\SystemWalletManager;
use App\Services\UserPaymentService;
use App\Services\XRPLPaymentService;
use App\Services\XRPLQueryService;
use App\Services\XRPLWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    protected $manager;

    public function __construct(SystemWalletManager $manager)
    {
        $this->manager = $manager;
    }

    public function create(Request $request, XRPLWalletService $service)
    {
        Log::info('Request', $request->all());

        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'email' => ['required'],
            'name' => ['required', 'string'],
        ]);

        Log::info($data);

        try {
            // This now handles Generation -> Activation -> TrustLine
            $wallet = $service->createForUser($data);

            return response()->json([
                'success' => true,
                'wallet_id' => $wallet->id,
                'address' => $wallet->address,
                'status' => $wallet->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function balance(Request $request)
    {
        $data = $request->validate([
            'address' => ['required', 'string'],
        ]);

        $wallet = Wallet::where('address', $data['address'])->first();

        if (! $wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $info = app(XRPLQueryService::class)->accountInfo($wallet);

        Log::info('Get Account Info', $info);

        return response()->json([
            'address' => $wallet->address,
            'balances' => $info,
        ]);
    }

    public function transactions(Request $request)
    {
        $data = $request->validate([
            'address' => ['required', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $wallet = Wallet::findOrFail($data['wallet_id']);

        $txs = app(XRPLQueryService::class)->transactions(
            $wallet,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null
        );

        return response()->json([
            'transactions' => $txs,
        ]);
    }

    public function sendPayment(Request $request)
    {
        Log::info('Request', $request->all());

        $data = $request->validate([
            'feeturre_order_id' => ['required', 'string'],
            'source' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
            // 'currency' => ['required', 'string'],
        ]);

        $wallet = Wallet::where('address', $data['source'])->first();

        if (! $wallet) {
            return response()->json(['error' => 'Source wallet not found'], 404);
        }

        Log::info("🧩 Using Wallet ID: {$wallet->id} for Order: {$data['feeturre_order_id']}");

        $currencyCode = config('xrpl.currency', 'FEE');

        $issuer = null;
        $nativeCurrency = config('xrpl.native_currency', 'XRP');

        if ($currencyCode !== $nativeCurrency) {
            $issuer = config('xrpl.cold_wallet.address');
        }

        $transaction = OrderTransaction::create([
            'order_id' => $data['feeturre_order_id'],
            'source' => $data['source'],
            'destination' => $data['destination'],
            'amount' => $data['amount'],
            'currency' => config('xrpl.currency'),
            'type' => 'payment',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $result = app(XRPLPaymentService::class)->send(
            $wallet,
            $data['destination'],
            $data['amount'],
            $transaction,
            $currencyCode,
            $issuer
        );

        return response()->json($result);
    }

    public function addFund(Request $request)
    {
        $data = $request->validate([
            'destination' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
        ]);

        $wallet = Wallet::where('address', $data['destination'])->first();

        if (! $wallet) {
            return response()->json(['error' => 'Destination wallet not found'], 404);
        }
        Log::info("🧩 Using Wallet ID: {$wallet->id} for Amount : {$data['amount']}");

        // dd($wallet);
        $currencyCode = config('xrpl.currency', 'FEE');

        $issuer = null;
        $nativeCurrency = config('xrpl.native_currency', 'XRP');

        if ($currencyCode !== $nativeCurrency) {
            $issuer = config('xrpl.cold_wallet.address');
        }

        $memos = xahau()->buildMemos([
            'type' => 'Add Fund',
            'amount' => $data['amount'],
        ]);

        $result = $this->manager->sendSystemPayment(
            'hot',
            $data['destination'],
            $data['amount'],
            $currencyCode,
            false,
            null,
            false,
            $memos);

        // Log::info($result);

        return response()->json($result);
    }

    public function sendXRP(Request $request)
    {
        $data = $request->validate([
            'destination' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
            'currency' => ['nullable', 'string'],
        ]);

        $wallet = Wallet::where('address', $data['destination'])->first();

        if (! $wallet) {
            return response()->json(['error' => 'Destination wallet not found'], 404);
        }

        Log::info("🧩 Using Wallet ID: {$wallet->id} for Amount : {$data['amount']}");

        $currencyCode = $data['currency'] ?? config('xrpl.currency', 'FEE');

        $result = $this->manager->sendSystemPayment('hot', $data['destination'], $data['amount'], $currencyCode);

        return response()->json($result);
    }


    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'address' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
        ]);

        $wallet = Wallet::where('address', $data['address'])->first();

        if (! $wallet) {
            return response()->json(['error' => 'Source wallet not found'], 404);
        }

        Log::info("🧩 Initiating Withdraw from Wallet ID: {$wallet->id} Amount: {$data['amount']}");

        $currencyCode = config('xrpl.currency', 'FEE');
        $nativeCurrency = config('xrpl.native_currency', 'XRP');

        $destination = config('xrpl.hot_wallet.address');

        $memos = xahau()->buildMemos([
            'type' => 'Withdraw',
            'amount' => $data['amount'],
        ]);

        try {

            $result = $this->manager->sendSystemPayment(
                'hot',
                $destination,
                $data['amount'],
                $currencyCode,
                false,
                $data['address'],
                true,
                $memos
            );

            Log::info('Withdrawal Result:', $result);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Withdrawal Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function sweepWallet(Request $request)
    {
        $data = $request->validate([
            'address' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
        ]);

        $wallet = Wallet::where('address', $data['address'])->first();
        $user = $wallet ? $wallet->user : null;

        if (! $wallet) {
            return response()->json(['error' => 'Source wallet not found'], 404);
        }

        Log::info("🧩 Initiating Sweep Wallet from Wallet ID: {$wallet->id} Amount: {$data['amount']}");

        $currencyCode = config('xrpl.currency', 'FEE');
        $nativeCurrency = config('xrpl.native_currency', 'XRP');

        $destination = config('xrpl.hot_wallet.address');

        $memos = xahau()->buildMemos([
            'type' => 'Sweep Wallet',
            'amount' => $data['amount'],
        ]);

        try {

            $result = $this->manager->sendSystemPayment(
                'hot',
                $destination,
                $data['amount'],
                $currencyCode,
                false,
                $data['address'],
                true,
                $memos
            );
            

            // Log::info('Sweep Wallet Result:', $result);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Sweep Wallet Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSystemWalletAddress(string $type)
    {
        if (! in_array($type, ['hot', 'cold'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid wallet type. Use "hot" or "cold".',
            ], 400);
        }

        try {
            $key = $type === 'cold' ? 'xrpl.cold_wallet' : 'xrpl.hot_wallet';
            $address = config("$key.address");

            if (empty($address)) {
                throw new \Exception("Address not configured for $type wallet.");
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'address' => $address,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function streamPayout(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'destination' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
            'currency' => ['nullable', 'string'],
        ]);

        $wallet = Wallet::where('address', $data['destination'])->first();

        if (! $wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Destination wallet not found in Private App',
            ], 404);
        }

        $currencyCode = $data['currency'] ?? config('xrpl.currency', 'FEE');

        Log::info('🚀 Processing Stream Payout', [
            'user_id' => $data['user_id'],
            'address' => $data['destination'],
            'amount' => $data['amount'],
        ]);

        // dd('1');

        try {
            $result = $this->manager->sendSystemPayment(
                'hot',
                $data['destination'],
                $data['amount'],
                $currencyCode
            );
            // Log::info('Stream Payout Result:', $result);


            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => 'Stream payout successful',
            ]);

        } catch (\Throwable $e) {
            Log::error('Stream Payout Ledger Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Blockchain transaction failed: '.$e->getMessage(),
            ], 422);
        }
    }

    public function sendUserExternal(Request $request, UserPaymentService $service)
    {
        $data = $request->validate([
            'source'      => ['required', 'string'],
            'destination' => ['required', 'string'],
            'amount'      => ['required', 'numeric', 'min:0.000001'],
        ]);

        return response()->json(
            $service->sendFromUser(
                $data['source'],
                $data['destination'],
                $data['amount']
            )
        );
    }
}
