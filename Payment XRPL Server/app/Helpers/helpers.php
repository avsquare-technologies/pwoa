<?php

use App\Helpers\XahauHelper;

if (!function_exists('xahau')) {

    function xahau(): XahauHelper
    {
        return new XahauHelper();
    }
}

function normalize_xrpl_currency(string $currency): string
{
    $currency = strtoupper($currency);

    if ($currency === 'XRP') {
        return 'XRP';
    }

    if (strlen($currency) === 3) {
        return $currency;
    }

    if (strlen($currency) >= 3 && strlen($currency) <= 20) {
        return str_pad(bin2hex($currency), 40, '0', STR_PAD_RIGHT);
    }

    return $currency;
}


function xrplAmountFieldXRP(
    float|string $amount,
    ?string $currency = null,
    ?string $issuer = null
) {
    $nativeCurrency = config('xrpl.native_currency', 'XRP');

    if ((float) $amount <= 0) {
        return '0';
    }

    $currency = $currency ?? $nativeCurrency;
    $currency = normalize_xrpl_currency($currency);


    if ($currency === $nativeCurrency) {
        return bcmul((string) $amount, '1000000', 0);
    }

    if (empty($issuer)) {
        throw new \InvalidArgumentException('Issuer is required for issued currency.');
    }
    return [
        'currency' => $currency,
        'issuer'   => $issuer,
        'value'    => (string) $amount,
    ];
}

function xrplAmountField($amount, $currency = null, $issuer = null)
{
    $nativeCurrency = config('xrpl.native_currency', 'XRP');
    $currencyCode   = $currency ?? config('xrpl.currency', 'FEE');
    $currencyCode   = normalize_xrpl_currency($currencyCode);
    $issuerAddress  = $issuer ?? config('xrpl.cold_wallet.address');

    if ($amount <= 0) {
        return '0';
    }

    if ($currencyCode === $nativeCurrency) {
        return bcmul((string) $amount, '1000000', 0);
    }

    return [
        'currency' => $currencyCode,
        'issuer'   => $issuerAddress,
        'value'    => (string) $amount,
    ];
}


function xrplSendMaxField(
    float|string $amount,
    string $currency,
    string $issuer
) {
    $currency = normalize_xrpl_currency($currency);
    $transferFeePercent = (float) config('xrpl.issuer_settings.transfer_fee_percent', 0);

    $multiplier = 1 + ($transferFeePercent / 100) + 0.01;

    $maxSpend = function_exists('bcmul')
        ? bcmul((string) $amount, (string) $multiplier, 8)
        : (string) ((float) $amount * $multiplier);

    return [
        'currency' => $currency,
        'issuer'   => $issuer,
        'value'    => $maxSpend,
    ];
}
