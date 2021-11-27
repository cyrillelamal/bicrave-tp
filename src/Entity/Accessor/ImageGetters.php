<?php

namespace App\Entity\Accessor;

use App\Entity\Product;
use DateTimeInterface;
use JetBrains\PhpStorm\Pure;

trait ImageGetters
{
    #[Pure] public function getId(): ?int
    {
        return $this->id;
    }

    #[Pure] public function getName(): ?string
    {
        return $this->name;
    }

    #[Pure] public function getUploadedAt(): ?DateTimeInterface
    {
        return $this->uploadedAt;
    }

    #[Pure] public function getProduct(): ?Product
    {
        return $this->product;
    }
}
