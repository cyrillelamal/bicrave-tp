<?php

namespace App\Entity\Accessor;

use App\Entity\Category;
use DateTimeInterface;

trait ProductSetters
{
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setCost(?int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function setRest(?int $rest): self
    {
        $this->rest = $rest;

        return $this;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function setPopularity(?float $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }
}
