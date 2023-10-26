<?php

declare(strict_types=1);

namespace App\Api;

use App\ConversionResults;
use App\Currency;
use App\CurrencyAmount;
use App\CurrencyCollection;
use FastForex\Client;

class FastForexCurrencyConversion
{
    private Client $fastForexClient;
    private const DIVISOR = 1000;

    public function __construct(string $apiKey)
    {
        $this->fastForexClient = new Client();
        $this->fastForexClient->setApiKey($apiKey);
    }

    public function conversion(
        Currency           $baseCurrency,
        CurrencyCollection $currencies,
        CurrencyAmount     $amount
    ): ConversionResults
    {
        $fastForexRates = $this->fastForexClient->fetchMulti(
            $baseCurrency->getIsoCode(),
            $currencies->getIsoCodes()
        );

        $results = new ConversionResults;
        foreach ($currencies->getIsoCodes() as $isoCode) {
            $rate = $fastForexRates->results->{$isoCode};
            $resultValue = ($rate * $amount->getIntAmount()) / self::DIVISOR;
            $results->setResultForCurrency($isoCode, $resultValue);
        }
        return $results;
    }

}
