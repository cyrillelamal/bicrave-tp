<?php

namespace App\Entity\Accessor;

use App\Entity\Order;
use App\Entity\Product;

trait ReservationGetters
{
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }
}
