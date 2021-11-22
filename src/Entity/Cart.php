<?php

namespace App\Entity;

use App\Entity\Accessor\CartGetters;
use App\Entity\Accessor\CartSetters;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    use CartGetters;
    use CartSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Demand::class, orphanRemoval: true)]
    private Collection $demands;

    #[ORM\OneToOne(inversedBy: 'cart', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[Pure] public function __construct()
    {
        $this->demands = new ArrayCollection();
    }

    public function addDemand(Demand $demand): self
    {
        if (!$this->demands->contains($demand)) {
            $this->demands[] = $demand;
            $demand->setCart($this);
        }

        return $this;
    }

    public function removeDemand(Demand $demand): self
    {
        if ($this->demands->removeElement($demand)) {
            // set the owning side to null (unless already changed)
            if ($demand->getCart() === $this) {
                $demand->setCart(null);
            }
        }

        return $this;
    }
}
