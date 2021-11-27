<?php

namespace App\Entity\Accessor;

use App\Entity\Category;
use App\Entity\Image;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

trait ProductGetters
{
    #[Pure] public function getId(): ?int
    {
        return $this->id;
    }

    #[Pure] public function getName(): ?string
    {
        return $this->name;
    }

    #[Pure] public function getCost(): ?int
    {
        return $this->cost;
    }

    #[Pure] public function getRest(): ?int
    {
        return $this->rest;
    }

    #[Pure] public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Pure] public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<Image>
     */
    #[Pure] public function getImages(): Collection
    {
        return $this->images;
    }

    #[Pure] public function getCategory(): ?Category
    {
        return $this->category;
    }

    #[Pure] public function getPopularity(): ?float
    {
        return $this->popularity;
    }

    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }
}
