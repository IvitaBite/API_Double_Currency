<?php

declare(strict_types=1);

namespace App;

class CurrencyAmount
{
    private int $amount;

    private const MULTIPLIER = 1000;
    public function __construct(float $amount)
    {
        $this->amount = (int) ($amount * self::MULTIPLIER);
    }

    public function getIntAmount(): int
    {
        return $this->amount;
    }
}
