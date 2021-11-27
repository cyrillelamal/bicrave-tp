<?php

namespace App\Entity\Accessor;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

trait CategoryGetters
{
    #[Pure] public function getId(): ?int
    {
        return $this->id;
    }

    #[Pure] public function getName(): ?string
    {
        return $this->name;
    }

    #[Pure] public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @return Collection<Category>
     */
    #[Pure] public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection<Product>
     */
    #[Pure] public function getProducts(): Collection
    {
        return $this->products;
    }
}
