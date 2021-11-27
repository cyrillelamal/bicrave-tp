<?php

namespace App\Entity\Accessor;

use App\Entity\Order;
use App\Entity\Product;

trait ReservationSetters
{
    public function setCost(?int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
