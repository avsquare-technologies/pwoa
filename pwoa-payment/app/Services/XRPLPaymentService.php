<?php

namespace App\Services;

use App\Models\OrderTransaction;
use App\Models\Wallet;
use Exception;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Illuminate\Support\Facades\Log;
use App\Traits\NormalizesXRPLCurrency;

use function Hardcastle\XRPL_PHP\Sugar\xrpToDrops;

class XRPLPaymentService
{
    use NormalizesXRPLCurrency;

    protected $connection;


    protected $manager;

    public function __construct(XrplConnection $connection, SystemWalletManager $manager)
    {
        $this->connection = $connection;
        $this->manager = $manager;
    }

    public function send(
        Wallet $from,
        string $destination,
        float $amount,
        OrderTransaction $transaction,
        ?string $currency = null,
        ?string $issuer = null
    ) {

        $ws = $this->connection->getClient();

        try {
            $buyerWallet = XRPLWallet::fromSeed($from->seed);
            $buyerAddress = $buyerWallet->getAddress();

            $seller = Wallet::where('address', $destination)->first();

            if (! $seller) {
                throw new Exception('Destination wallet not found');
            }

            $sellerWallet = XRPLWallet::fromSeed($seller->seed);
            $sellerAddress = $destination;

            $creatorPct = (float) config('transactions.download.creator_pct', 0);
            $treasuryPct = (float) config('transactions.download.treasury_pct', 0);

            $creatorAmount = round($amount * $creatorPct / 100, 6);
            $treasuryAmount = round($amount * $treasuryPct / 100, 6);

            Log::info('XRPL Payment', [
                'from' => $buyerAddress,
                'to' => $sellerAddress,
                'amount' => $amount,
                'currency' => $currency,
            ]);

            $nativeCurrency = config('xrpl.native_currency', 'XRP');
            $currency = $currency ?? $nativeCurrency;
            $currency = $this->normalizeCurrency($currency);
            
            $sendMaxField = null;

            if ($currency === $nativeCurrency) {
                $amountField = (string) xrpToDrops($amount);

            } else {

                if (! $issuer) {
                    throw new Exception('Issuer required for issued currency');
                }

                $amountField = [
                    'currency' => $currency,
                    'issuer' => $issuer,
                    'value' => (string) $amount,
                ];

                $transferFeePercent = (float) config('xrpl.issuer_settings.transfer_fee_percent', 0);

                $multiplier = 1 + ($transferFeePercent / 100) + 0.01;

                if (function_exists('bcmul')) {
                    $maxSpendValue = bcmul((string) $amount, (string) $multiplier, 8);
                } else {
                    $maxSpendValue = (string) ($amount * $multiplier);
                }

                $sendMaxField = [
                    'currency' => $currency,
                    'issuer' => $issuer,
                    'value' => $maxSpendValue,
                ];
            }

            $accountInfo = $ws->request([
                'command' => 'account_info',
                'account' => $buyerAddress,
                'ledger_index' => 'current',
            ]);

            if (! isset($accountInfo['result']['account_data']['Sequence'])) {
                throw new Exception('Sender account not funded.');
            }

            $sequence = $accountInfo['result']['account_data']['Sequence'];

            $memos = xahau()->buildMemos([
                'OrderId' => $transaction->order_id,
                'Type' => 'Buy Feeturre'
            ]);

            $tx = [
                'TransactionType' => 'Payment',
                'Account' => $buyerAddress,
                'Destination' => $sellerAddress,
                'Amount' => $amountField,
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
            ];

            if ($sendMaxField) {
                $tx['SendMax'] = $sendMaxField;
            }

            if (! empty($memos)) {
                $tx['Memos'] = $memos;
            }

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = config('xrpl.network.testnet.id', 21338);
            }

            if (!isset($tx['SourceTag']) && $this->connection) {
                $tx['SourceTag'] = $this->connection->getSourceTag();
            }

            $signed = $buyerWallet->sign($tx);


            $submit = $ws->request([
                'command' => 'submit',
                'tx_blob' => $signed['tx_blob'],
            ]);

            // Log::info('XRPL submit result', $submit);

            $result = $submit['result'] ?? [];
            // Log::info('XRPL submit result', $result);
            $engineResult = $result['engine_result'] ?? 'failed';

            $isSuccess = in_array($engineResult, [
                'tesSUCCESS',
                'temREDUNDANT',
                'terQUEUED',
            ]);


            $transaction->update([
                'tx_hash' => $result['tx_json']['hash'] ?? null,
                'status' => $isSuccess ? 'success' : 'failed',
                'response' => $result,
                'validated_at' => now(),
            ]);

            if ($isSuccess && $treasuryAmount > 0) {

                try {

                    $this->manager->sendToHotWallet(
                        $sellerWallet,
                        $sellerAddress,
                        $treasuryAmount,
                        $currency,
                        $issuer,
                        $transaction->order_id,
                        $treasuryPct
                    );
                } catch (\Throwable $e) {
                    Log::warning('Treasury payout failed', [
                        'error' => $e->getMessage(),
                        'amount' => $treasuryAmount,
                    ]);
                }
            }
            return $result;
        } catch (\Throwable $e) {
            Log::error('XRPL payment failed', [
                'error' => $e->getMessage(),
                'from' => $from->address,
                'to' => $destination,
            ]);
            return [
                'engine_result' => 'error',
                'message' => $e->getMessage(),
            ];
        } finally {
            $ws->close();
        }
    }
}
