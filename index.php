<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Currency;
use App\CurrencyAmount;
use App\CurrencyCollection;
use App\CurrencyConversion;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$baseCurrencyIsoCode = '';
$targetCurrencyIsoCodes = '';
$amount = 0.0;

while (empty($baseCurrency) || empty($targetCurrency)) {
    $input = readline("Enter the amount and base currency (e.g., 100 USD): ");
    list($amount, $baseCurrency) = sscanf($input, "%f %s");

    if (empty($baseCurrency)) {
        echo "No base currency was entered. Try again.\n";
    } else {
        $targetCurrency = readline("Enter the target currency for conversion: ");

        if (empty($targetCurrency)) {
            echo "No target currency was entered. Try again.\n";
        } else {
            $baseCurrencyIsoCode = strtoupper($baseCurrency);
            $targetCurrencyIsoCodes = strtoupper($targetCurrency);
        }
    }
}
$conversion = new CurrencyConversion();
$targetCurrencyIsoCodes = explode(' ', $targetCurrencyIsoCodes);
$targetCurrencies = new CurrencyCollection($targetCurrencyIsoCodes);
$currencyAmount = new CurrencyAmount($amount);

$results = $conversion->conversion(
    new Currency($baseCurrencyIsoCode),
    $targetCurrencies,
    $currencyAmount
);

foreach ($results as $isoCode => $value)
{
    echo "{$isoCode} -> {$value}\n";
}
