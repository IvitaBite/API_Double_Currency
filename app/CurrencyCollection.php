<?php

declare(strict_types=1);

namespace App;

class CurrencyCollection
{
    private array $currencies;

    public function __construct(array $currencies = [])
    {
        foreach ($currencies as $isoCode)
        {
            $this->add(new Currency($isoCode));
        }
    }

    public function add(Currency $currency): void
    {
        $this->currencies[] = $currency;
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function getIsoCodes(): array
    {
        $isoCodes = [];
        foreach ($this->getCurrencies() as $currency)
        {
            /** @var  Currency $currency */
            $isoCodes[] = $currency->getIsoCode();
        }
        return $isoCodes;
    }
}
