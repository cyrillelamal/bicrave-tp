<?php

namespace App\Common\Currency;

interface CurrencyFormatterInterface
{
    /**
     * Format the provided price.
     *
     * @param float|int $price the final price. This value shouldn't be modified.
     * @return string
     */
    public function format(float|int $price): string;
}
