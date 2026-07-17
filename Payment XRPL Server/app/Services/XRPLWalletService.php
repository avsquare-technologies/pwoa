<?php

namespace App\Services;

use App\Models\Wallet;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\NormalizesXRPLCurrency;

class XRPLWalletService
{
    use NormalizesXRPLCurrency;

    protected $systemWallet;

    protected $connection;

    public function __construct(SystemWalletManager $systemWallet, XrplConnection $connection)
    {
        $this->systemWallet = $systemWallet;
        $this->connection = $connection;
    }

    public function createForUser(array $user): Wallet
    {
        $userId = $user['user_id'];
        $userEmail = strtolower(trim($user['email']));
        $userName = $user['name'] ?? "User";

        $wallet = Wallet::where('user_id', $userId)->first();

        if ($wallet) {
            Log::info("User Wallet Already Exists ($userId): " . $wallet->address);
            return $wallet;
        }

        $userXrplWallet = XRPLWallet::generate();
        $newAddress = $userXrplWallet->getAddress();
        $userSeed = $userXrplWallet->getSeed();

        $wallet = Wallet::create([
            'user_id' => $userId,
            'email' => $userEmail,
            'name' => $userName,
            'address' => $newAddress,
            'seed' => $userSeed,
            'private_key' => $userXrplWallet->getPrivateKey(),
            'public_key' => $userXrplWallet->getPublicKey(),
            'status' => 'inactive',
        ]);

        Log::info("Created User Wallet ($userId): " . $newAddress);

        try {
            $activationAmount = config('xrpl.activation_amount', 4);
            $this->systemWallet->sendSystemPayment('hot', $newAddress, $activationAmount);

            $this->systemWallet->ensureAccountActive($newAddress);

            $customCurrency = config('xrpl.currency', 'FEE');
            $this->ensureTrustLineForUser($userSeed, $customCurrency);

            $wallet->update(['status' => 'active']);
        } catch (Exception $e) {
            Log::error("User Wallet Activation Failed ($userId): " . $e->getMessage());
            $wallet->update(['status' => 'failed']);
            throw $e;
        }

        return $wallet;
    }

    public function ensureTrustLineForUser(string $userSeed, string $currencyCode, ?string $issuerAddress = null)
    {
        $currencyCode = $this->normalizeCurrency($currencyCode);

        if (!$issuerAddress) {
            $issuerCreds = $this->systemWallet->getCredentials('cold');
            $issuerAddress = $issuerCreds['address'];
        }



        return $this->systemWallet->submitTransaction($userSeed, function ($sequence, $signerAddress) use ($currencyCode, $issuerAddress) {

            $tx = [
                'TransactionType' => 'TrustSet',
                'Account' => $signerAddress,
                'LimitAmount' => [
                    'currency' => $currencyCode,
                    'issuer' => $issuerAddress,
                    'value' => '10000000000',
                ],
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
            ];

            // Inject NetworkID ONLY for Xahau
            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = $this->connection->getNetworkId();
            }

            return $tx;
        });
    }

    public function issueTokensToUser(string $userAddress, float $amount, string $currency)
    {
        return $this->systemWallet->sendSystemPayment('cold', $userAddress, $amount, $currency);
    }
}
