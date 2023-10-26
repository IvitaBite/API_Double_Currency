<?php

declare(strict_types=1);

namespace App;

class ConversionResults
{
    private array $results;

    public function __construct(array $results = [])
    {
        $this->results = $results;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setResultForCurrency(string $isoCode, float $value): void
    {
        $this->results[$isoCode] = $value;
    }
}