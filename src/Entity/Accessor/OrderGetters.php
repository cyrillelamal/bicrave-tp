<?php

namespace App\Entity\Accessor;

use App\Entity\User;
use DateTimeInterface;

trait OrderGetters
{
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }
}
