<?php

namespace App\Traits;

trait HandlesXrplCurrency
{
    /**
     * Encode a currency code for XRPL payloads.
     * Standard 3-char codes are returned as-is.
     * Longer codes (like WASH) are converted to 160-bit hex.
     *
     * @param string $currency
     * @return string
     */
    protected function encodeCurrency(string $currency): string
    {
        if (strlen($currency) === 3) {
            return $currency;
        }

        // Convert to 160-bit hex string (40 characters)
        $hex = strtoupper(bin2hex($currency));
        return str_pad($hex, 40, '0', STR_PAD_RIGHT);
    }

    /**
     * Decode a currency code from XRPL.
     *
     * @param string $currency
     * @return string
     */
    protected function decodeCurrency(string $currency): string
    {
        if (strlen($currency) === 3) {
            return $currency;
        }

        if (strlen($currency) === 40) {
            $decoded = rtrim(@hex2bin($currency), "\0");
            return $decoded ?: $currency;
        }

        return $currency;
    }

    /**
     * Check if a currency from the ledger matches our target currency.
     * Handles both plain text and hex-encoded ledger values.
     *
     * @param string $ledgerCurrency
     * @param string $targetCurrency
     * @return bool
     */
    protected function isCurrencyMatch(string $ledgerCurrency, string $targetCurrency): bool
    {
        // 1. Exact match
        if ($ledgerCurrency === $targetCurrency) {
            return true;
        }

        // 2. Hex match (if ledger value is 40-char hex)
        if (strlen($ledgerCurrency) === 40) {
            $decoded = rtrim(@hex2bin($ledgerCurrency), "\0");
            if ($decoded === $targetCurrency) {
                return true;
            }
        }

        return false;
    }

    public function usdToWash(float|int $amount, int $precision = 6): string
    {
        $rate = config('services.xrpl.wash_to_usd', 0.05);
        if ($rate <= 0) return '0.000000';
        return number_format((float) $amount / (float) $rate, $precision, '.', '');
    }

    public function washToUsd(float|int $amount, int $precision = 2): string
    {
        $rate = config('services.xrpl.wash_to_usd', 0.05);
        return number_format((float) $amount * (float) $rate, $precision, '.', '');
    }
}
