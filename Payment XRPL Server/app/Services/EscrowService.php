<?php

namespace App\Services;

use App\Models\OrderEscrow;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\NormalizesXRPLCurrency;

class EscrowService
{
    use NormalizesXRPLCurrency;

    protected $walletManager;


    protected $escrowWalletAddress;

    protected $connection;

    public function __construct(SystemWalletManager $walletManager, XrplConnection $connection)
    {
        $this->walletManager = $walletManager;
        $this->escrowWalletAddress = config('xrpl.escrow_wallet.address');
        $this->connection = $connection;
    }

    public function createEscrow(int $orderId, string $buyerAddress, string $sellerAddress, float $amount, float $amountUsd, string $expiresAt)
    {
        $expires = Carbon::parse($expiresAt);
        $checkExpiryDays = (int) config('xrpl.check_expiration', 7);
        $checkExpiry = $expires->copy()->addDays($checkExpiryDays)->timestamp;
        $currency = config('xrpl.currency', 'FEE');
        $currency = $this->normalizeCurrency($currency);


        return DB::transaction(function () use ($orderId, $buyerAddress, $sellerAddress, $amount, $checkExpiry, $currency, $amountUsd) {

            $memos = xahau()->buildMemos([
                'type' => 'Create Skill',
                'orderId' => $orderId,
            ]);

            try {
                $this->walletManager->sendSystemPayment('escrow', $this->escrowWalletAddress, $amount, $currency, true, $buyerAddress, false, $memos);
            } catch (Exception $e) {
                throw new \RuntimeException('Funding failed: ' . $e->getMessage());
            }

            ['seed' => $adminSeed] = $this->walletManager->getCredentials('escrow');
            ['address' => $issuer] = $this->walletManager->getCredentials('cold');

            $txResult = $this->walletManager->submitTransaction($adminSeed, function ($sequence, $signerAddress) use ($sellerAddress, $amount, $currency, $issuer, $checkExpiry, $orderId, $buyerAddress) {

                $transferRatePercent = (float) config('xrpl.issuer_settings.transfer_fee_percent', 0);
                $multiplier = 1 + ($transferRatePercent / 100);
                $totalWithFee = bcmul((string) $amount, (string) $multiplier, 8);

                $tx = [
                    'TransactionType' => 'CheckCreate',
                    'Account' => $signerAddress,
                    'Destination' => $sellerAddress,
                    'SendMax' => [
                        'currency' => $currency,
                        'issuer' => $issuer,
                        'value' => $totalWithFee,
                    ],
                    'Expiration' => $checkExpiry - 946684800,
                    'Sequence' => $sequence,
                    'Fee' => $this->connection->getFee(),
                ];
                if (config('xrpl.network_name') === 'xahau') {
                    $tx['NetworkID'] = config('xrpl.network_id');
                }

                $tx['Memos'] = xahau()->buildMemos([
                    'escrow'   => "order:$orderId",
                    'buyer'    => $buyerAddress,
                ]);

                return $tx;
            });

            $checkId = $this->extractCheckId($txResult);

            return OrderEscrow::create([
                'order_id' => $orderId,
                'buyer_address' => $buyerAddress,
                'seller_address' => $sellerAddress,
                'amount' => $amount,
                'amount_usd' => $amountUsd,
                'status' => OrderEscrow::FUNDED,
                'tx_hash' => $checkId,
                'expires_at' => Carbon::createFromTimestamp($checkExpiry),
            ]);
        });
    }

    public function finishEscrow(int $orderId)
    {
        Log::info('Finishing escrow', ['orderId' => $orderId]);
        $escrow = OrderEscrow::where('order_id', $orderId)->firstOrFail();
        $currency = config('xrpl.currency', 'FEE');
        $currency = $this->normalizeCurrency($currency);


        // $creatorPct = (float) config('transactions.escrow.creator_pct', 98.5);
        // $treasuryPct = (float) config('transactions.escrow.treasury_pct', 1.5);

        // $treasuryAmount = round($escrow->amount * $treasuryPct / 100, 6);

        $amountUsd = (float) $escrow->amount_usd;

        // Get treasury % from tier based on USD
        $treasuryPct = $this->resolveSkillCommissionRate($amountUsd);

        // Creator gets remainder
        $creatorPct = max(0, 100 - $treasuryPct);

        // Calculate distribution based on XRPL token amount
        $treasuryAmount = round($escrow->amount * $treasuryPct / 100, 6);
        $creatorAmount  = round($escrow->amount * $creatorPct / 100, 6);

        $sellerWallet = Wallet::where('address', $escrow->seller_address)->firstOrFail();
        ['address' => $issuer] = $this->walletManager->getCredentials('cold');
        ['address' => $hotWallet] = $this->walletManager->getCredentials('hot');

        $result = $this->walletManager->submitTransaction($sellerWallet->seed, function ($sequence, $signerAddress) use ($escrow, $currency, $issuer, $treasuryAmount, $creatorAmount, $hotWallet) {
            $tx = [
                'TransactionType' => 'CheckCash',
                'Account' => $signerAddress,
                'CheckID' => $escrow->tx_hash,
                'Amount' => [
                    'currency' => $currency,
                    'issuer' => $issuer,
                    'value' => (string) $creatorAmount,
                ],
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = $this->connection->getNetworkId();
            }

            $tx['Memos'] = xahau()->buildMemos([
                'Order'   => "order:$escrow->order_id",
                'type'     => 'release',
                'FeeAmount' => $treasuryAmount
            ]);

            return $tx;
        });

        // Log::info('Escrow Released', [
        //     'order_id' => $orderId,
        //     'result' => $result,
        // ]);

        // if (
        //     isset($result['result']['validated']) &&
        //     $result['result']['validated'] === true &&
        //     $result['result']['meta']['TransactionResult'] === 'tesSUCCESS'
        // ) {

        //     if ($treasuryAmount > 0) {
        //         try {
        //             $memos = xahau()->buildMemos([
        //                 'Order'   => "order:$orderId",
        //                 'buyer'    => $escrow->buyer_address,
        //                 'type'     => 'treasury',
        //                 'percent'  => $treasuryPct
        //             ]);
        //             $this->walletManager->sendSystemPayment(
        //                 'hot',
        //                 $hotWallet,
        //                 $treasuryAmount,
        //                 $currency,
        //                 true,
        //                 $sellerWallet->address,
        //                 false,
        //                 $memos
        //             );

        //             Log::info('Escrow Treasury Fee Distributed', [
        //                 'order_id' => $orderId,
        //                 'amount' => $treasuryAmount,
        //             ]);
        //         } catch (Exception $e) {
        //             Log::error('Treasury fee distribution failed: ' . $e->getMessage());
        //         }
        //     }
        // }

        $escrow->update(['status' => OrderEscrow::RELEASED]);

        return ['success' => true, 'tx' => $result];
    }

    public function cancelEscrow(int $orderId)
    {
        return DB::transaction(function () use ($orderId) {
            $escrow = OrderEscrow::where('order_id', $orderId)->firstOrFail();
            $currency = config('xrpl.currency', 'FEE');
            $currency = $this->normalizeCurrency($currency);


            ['seed' => $adminSeed] = $this->walletManager->getCredentials('escrow');

            $amountUsd = (float) $escrow->amount_usd;

            // Get treasury % from tier based on USD
            // $treasuryPct = $this->resolveSkillCommissionRate($amountUsd);

            // // Creator gets remainder
            // $creatorPct = max(0, 100 - $treasuryPct);

            // // Calculate distribution based on XRPL token amount
            // $treasuryAmount = round($escrow->amount * $treasuryPct / 100, 6);
            // $creatorAmount  = round($escrow->amount * $creatorPct / 100, 6);

            $totalAmount = (float) $escrow->amount;

            $feeRate = $this->resolveSkillCommissionRate($amountUsd);

            $creatorAmount = round($totalAmount / (1 + ($feeRate / 100)), 6);
            $treasuryAmount = round($totalAmount - $creatorAmount, 6);

            try {
                $this->walletManager->submitTransaction($adminSeed, function ($sequence, $signerAddress) use ($escrow) {
                    $tx = [
                        'TransactionType' => 'CheckCancel',
                        'Account' => $signerAddress,
                        'CheckID' => $escrow->tx_hash,
                        'Sequence' => $sequence,
                        'Fee' => $this->connection->getFee(),
                    ];

                    if (config('xrpl.network_name') === 'xahau') {
                        $tx['NetworkID'] = $this->connection->getNetworkId();
                    }

                    return $tx;
                });
            } catch (Exception $e) {
                Log::warning('CheckCancel failed, continuing to refund: ' . $e->getMessage());
            }

            $memos = xahau()->buildMemos([
                'type' => 'refund',
                'Order' => "order:$orderId",
                'FeeAmount' => $treasuryAmount,
                'FeePercent' => $feeRate . '%',
            ]);

            $this->walletManager->sendSystemPayment(
                'escrow',
                $escrow->buyer_address,
                $creatorAmount,
                $currency,
                false,
                null,
                false,
                $memos
            );

            $escrow->update(['status' => OrderEscrow::CANCELED]);

            return ['success' => true];
        });
    }

    private function extractCheckId(array $result): string
    {
        $meta = $result['result']['meta'] ?? $result['result']['metadata'] ?? $result['meta'] ?? null;

        if (! $meta || ! isset($meta['AffectedNodes'])) {
            Log::error('Full TX Result for debugging:', $result);
            throw new Exception('Transaction metadata missing. Check result or Ledger status.');
        }

        foreach ($meta['AffectedNodes'] as $node) {
            if (isset($node['CreatedNode']) && $node['CreatedNode']['LedgerEntryType'] === 'Check') {
                return $node['CreatedNode']['LedgerIndex'];
            }

            if (isset($node['ModifiedNode']) && $node['ModifiedNode']['LedgerEntryType'] === 'Check') {
                return $node['ModifiedNode']['LedgerIndex'];
            }
        }

        throw new Exception('Check object not created in ledger. The transaction may have failed or the destination did not accept the check.');
    }

    private function resolveSkillCommissionRate(float $amountUsd): float
    {
        $tiers = config('transactions.skill.tiers', []);

        foreach ($tiers as $tier) {
            if ($amountUsd >= $tier['min'] && $amountUsd <= $tier['max']) {
                return (float) $tier['rate'];
            }
        }

        return 0.0;
    }
}
