<?php

namespace App\Entity\Accessor;

use App\Entity\Cart;
use App\Entity\Product;

trait DemandSetters
{
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }
}