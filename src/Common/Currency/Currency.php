<?php

namespace App\Common\Currency;

use JetBrains\PhpStorm\Pure;

class Currency
{
    private string $code;

    /**
     * @param string $code ISO 4217 code
     */
    #[Pure] public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Get the ISO 4217 code.
     */
    #[Pure] public function getCode(): string
    {
        return strtoupper($this->code);
    }

    #[Pure] public function __toString(): string
    {
        return $this->getCode();
    }
}
