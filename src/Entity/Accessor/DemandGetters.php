<?php

namespace App\Entity\Accessor;

use App\Entity\Cart;
use App\Entity\Product;

trait DemandGetters
{
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }
}
