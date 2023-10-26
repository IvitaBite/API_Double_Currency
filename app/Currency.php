<?php

declare(strict_types=1);

namespace App;

class Currency
{
    private string $isoCode;
    public function __construct(string $isoCode)
    {
        $this->isoCode = $isoCode;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }
}
