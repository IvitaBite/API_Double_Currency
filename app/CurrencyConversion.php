<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;

class CurrencyConversion
{
    private Client $client;
    private const API_URL = "https://api.freecurrencyapi.com/v1/latest?";
    public function __construct()
    {
        $this->client = new Client();
    }

    private function getUrl(Currency $baseCurrency, CurrencyCollection $currencies): string
    {
        $params = [
            'apikey' => $_ENV['API_KEY'],
            'currencies' => implode(',', $currencies->getIsoCodes()),
            'base_currency' => $baseCurrency->getIsoCode()
        ];
        return self::API_URL . http_build_query($params);
    }

    public function conversion(
        Currency $baseCurrency,
        CurrencyCollection $currencies,
        CurrencyAmount $amount)
    : array // /: ConversionResult
    {
        $url = $this->getUrl($baseCurrency, $currencies);
        $result = $this->client->get($url);
        $result = $result->getBody()->getContents();
        $result = json_decode($result, true);

        $results = []; 
        foreach ($result['data'] as $isoCode => $rate)
        {
            $results[$isoCode] = $rate * $amount->getIntAmount(); // private const DIVISOR = 1000 /self::DIVISOR
        }
        return $results;
    }

}
