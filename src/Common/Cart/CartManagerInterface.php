<?php

namespace App\Common\Cart;

use App\Entity\Cart;

interface CartManagerInterface
{
    /**
     * Persist the provided shopping crt.
     *
     * @param Cart $cart
     */
    public function save(Cart $cart): void;

    /**
     * Remove the shopping cart demands.
     *
     * @param Cart $cart
     */
    public function clear(Cart $cart): void;
}
