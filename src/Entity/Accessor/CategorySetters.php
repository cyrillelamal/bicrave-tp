<?php

namespace App\Entity\Accessor;

use App\Entity\Category;

trait CategorySetters
{
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setParent(?Category $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
