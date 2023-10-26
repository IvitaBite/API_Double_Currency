<?php

declare(strict_types=1);

namespace App\Api;

use App\ConversionResults;
use App\Currency;
use App\CurrencyAmount;
use App\CurrencyCollection;
use GuzzleHttp\Client;

class FreeCurrencyCurrencyConversion
{
    private Client $client;
    private const DIVISOR = 1000;
    private const API_URL = "https://api.freecurrencyapi.com/v1/latest?";

    public function __construct()
    {
        $this->client = new Client();
    }

    private function getUrl(Currency $baseCurrency, CurrencyCollection $currencies): string
    {
        $params = [
            'apikey' => $_ENV['FREECURRENCY_API_KEY'],
            'currencies' => implode(',', $currencies->getIsoCodes()),
            'base_currency' => $baseCurrency->getIsoCode()
        ];
        return self::API_URL . http_build_query($params);
    }

    public function conversion(
        Currency           $baseCurrency,
        CurrencyCollection $currencies,
        CurrencyAmount     $amount
    ): ?ConversionResults
    {
        $url = $this->getUrl($baseCurrency, $currencies);
        $result = $this->client->get($url);
        $result = json_decode($result->getBody()->getContents(), true);

        if ($result === false) {
            echo "Error: JSON decoding failed. The response body could not be decoded as JSON.";
            return null;
        }

        if ($result === null) {
            echo "Error: JSON decoding returned null. The response body may not be valid JSON.";
            return null;
        }

        $results = new ConversionResults;
        foreach ($result['data'] as $isoCode => $rate) {
            $resultValue = ($rate * $amount->getIntAmount()) / self::DIVISOR;
            $results->setResultForCurrency($isoCode, $resultValue);
        }
        return $results;
    }

}
