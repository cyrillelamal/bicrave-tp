<?php

namespace App\Common\Cart;

use App\Entity\Cart;

interface MergingStrategy
{
    /**
     * Merge a few shopping carts into a single one.
     *
     * @param Cart ...$carts
     * @return Cart
     */
    public function merge(Cart ...$carts): Cart;
}
