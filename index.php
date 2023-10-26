<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Api\FastForexCurrencyConversion;
use App\Api\FreeCurrencyCurrencyConversion;
use App\Api\MetalPriceCurrencyConversion;
use App\Currency;
use App\CurrencyAmount;
use App\CurrencyCollection;
use App\IsoCodes;
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

$targetCurrencyIsoCodes = explode(' ', $targetCurrencyIsoCodes);
$targetCurrencies = new CurrencyCollection($targetCurrencyIsoCodes);
$currencyAmount = new CurrencyAmount($amount);

$freeCurrencyConversion = new FreeCurrencyCurrencyConversion();
$freeCurrencyApiResults = $freeCurrencyConversion->conversion(
    new Currency($baseCurrencyIsoCode),
    $targetCurrencies,
    $currencyAmount
);

$fastForexApiKey = $_ENV['FASTFROREX_API_KEY'];
$fastForexConversion = new FastForexCurrencyConversion($fastForexApiKey);
$fastForexApiResults = $fastForexConversion->conversion(
    new Currency($baseCurrencyIsoCode),
    $targetCurrencies,
    $currencyAmount
);

$metalPriceConversion = new MetalPriceCurrencyConversion();
$metalPriceApiResults = $metalPriceConversion->conversion(
    new Currency($baseCurrencyIsoCode),
    $targetCurrencies,
    $currencyAmount
);

$mostProfitableSources = [];
$highestRates = [];
$isoCodes = (new IsoCodes())->get();
foreach ($targetCurrencies->getIsoCodes() as $isoCode) {
    $highestRates[$isoCode] = 0.0;
}

foreach ($freeCurrencyApiResults->getResults() as $isoCode => $value) {
    if ($value > $highestRates[$isoCode]) {
        $mostProfitableSources[$isoCode] = 'FreeCurrency API (https://app.freecurrencyapi.com/)';
        $highestRates[$isoCode] = $value;
    }
}
foreach ($fastForexApiResults->getResults() as $isoCode => $value) {
    if ($value > $highestRates[$isoCode]) {
        $mostProfitableSources[$isoCode] = "FastForex API (https://www.fastforex.io/)";
        $highestRates[$isoCode] = $value;
    }
}

foreach ($metalPriceApiResults->getResults() as $isoCode => $value) {
    if ($value > $highestRates[$isoCode]) {
        $mostProfitableSources[$isoCode] = "MetalPrice API (https://metalpriceapi.com/)";
        $highestRates[$isoCode] = $value;
    }
}

echo "Currency Conversion Results\n";
echo "--------------------------------\n";

foreach ($targetCurrencies->getIsoCodes() as $isoCode) {
    $currencyName = $isoCodes[$isoCode];
    echo sprintf("%-10s %-12s %-30s\n", "Currency",
        str_pad("Rate", 5, " ", STR_PAD_BOTH),
        str_pad("Source", 10, " ", STR_PAD_BOTH));
    echo str_repeat("-", 40) . "\n";
    echo sprintf("%-10s %-12s %-30s\n", str_pad("$isoCode", 7, " ", STR_PAD_BOTH),
        number_format($freeCurrencyApiResults->getResults()[$isoCode], 2), "FreeCurrency API");
    echo sprintf("%-10s %-12s %-30s\n", str_pad("$isoCode", 7, " ", STR_PAD_BOTH),
        number_format($fastForexApiResults->getResults()[$isoCode], 2), "FastForex API");
    echo sprintf("%-10s %-12s %-30s\n", str_pad("$isoCode", 7, " ", STR_PAD_BOTH),
        number_format($metalPriceApiResults->getResults()[$isoCode], 2), "MetalPrice API");
    echo str_repeat("-", 40) . "\n";
    echo "For currency $currencyName ($isoCode), the most profitable source is " . $mostProfitableSources[$isoCode];
    echo "\n--------------------------------\n";
}
