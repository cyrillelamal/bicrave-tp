<?php

namespace App\Entity\Accessor;

use App\Entity\Reservation;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;

trait OrderSetters
{
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

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }
}
