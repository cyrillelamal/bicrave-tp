<?php

namespace App\Entity\Accessor;

use App\Entity\User;

trait CartSetters
{
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
