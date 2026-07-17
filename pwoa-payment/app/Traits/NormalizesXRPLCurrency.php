<?php

namespace App\Traits;

trait NormalizesXRPLCurrency
{
    /**
     * Normalizes currency codes for XRPL.
     * 3-character codes are kept as is.
     * 4-character or longer codes are hex-encoded (40 hex characters).
     */
    protected function normalizeCurrency(string $currency): string
    {
        return normalize_xrpl_currency($currency);
    }

}
