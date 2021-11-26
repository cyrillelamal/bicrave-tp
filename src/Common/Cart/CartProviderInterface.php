<?php

namespace App\Common\Cart;

use App\Entity\Cart;

interface CartProviderInterface
{
    /**
     * Check if this provider is able to provide a shopping cart.
     *
     * @return bool
     */
    public function canProvideCart(): bool;

    /**
     * Get the shopping cart.
     *
     * @return Cart
     */
    public function getCart(): Cart;
}
