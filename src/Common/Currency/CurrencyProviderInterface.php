<?php

namespace App\Common\Currency;

interface CurrencyProviderInterface
{
    /**
     * Get the application currency.
     *
     * @return Currency
     */
    public function getCurrency(): Currency;
}
