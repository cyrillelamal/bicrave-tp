<?php

namespace App\Entity\Accessor;

use App\Entity\Demand;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

trait CartGetters
{
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<Demand>
     */
    public function getDemands(): Collection
    {
        return $this->demands;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
}
