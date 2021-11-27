<?php

namespace App\Entity\Accessor;

use App\Entity\Product;
use DateTimeInterface;

trait ImageSetters
{
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setUploadedAt(?DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
