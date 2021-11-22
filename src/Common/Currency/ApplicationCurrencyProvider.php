<?php

namespace App\Common\Currency;

use JetBrains\PhpStorm\Pure;

class ApplicationCurrencyProvider implements CurrencyProviderInterface
{
    private string $code;

    #[Pure] public function __construct(
        string $code,
    )
    {
        $this->code = $code;
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function getCurrency(): Currency
    {
        return new Currency($this->getCode());
    }

    #[Pure] protected function getCode(): string
    {
        return $this->code;
    }
}
