<?php

namespace App\Entity;

use App\Entity\Accessor\CartGetters;
use App\Entity\Accessor\CartSetters;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    public const READ = 'cart:read';

    use CartGetters;
    use CartSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Demand::class, orphanRemoval: true)]
    #[Groups([self::READ])]
    private Collection $demands;

    #[ORM\OneToOne(inversedBy: 'cart', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * Make a new shopping cart.
     * Simple factory.
     *
     * @param User $user the owner of the shopping cart.
     * @return static
     */
    public static function make(User $user): self
    {
        $cart = new self();

        $cart->setOwner($user);
        $user->setCart($cart);

        return $cart;
    }

    #[Pure] public function __construct()
    {
        $this->demands = new ArrayCollection();
    }

    /**
     * Get the shopping cart total cost.
     *
     * @return int cents.
     */
    #[Groups([Cart::READ])]
    #[Pure] public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getDemands() as $demand) /** @var Demand $demand */ {
            $total += $demand->getTotal();
        }

        return $total;
    }

    /**
     * Get the final shopping cart price.
     *
     * @return float not formatted application currency value.
     */
    #[Pure] public function getPrice(): float
    {
        return $this->getTotal() / 100;
    }

    /**
     * Check if this shopping cart has no demanded products.
     */
    public function isEmpty(): bool
    {
        return $this->getDemands()->isEmpty();
    }

    /**
     * Check if this shopping cart contains the provided product.
     * @param Product|Demand $demanded the demanded product or a demand referencing id.
     */
    #[Pure] public function contains(Product|Demand $demanded): bool
    {
        $product = $demanded instanceof Demand ? $demanded->getProduct() : $demanded;

        foreach ($this->getDemands() as $demand) /** @var Demand $demand */ {
            if ($demand->representsProduct($product)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the demand that represents the provided product.
     *
     * @param Product|Demand $demanded the demanded product or a demand referencing it.
     * @return Demand the existing demand or a completely new one if the product has not been demanded.
     */
    public function getDemandOf(Product|Demand $demanded): Demand
    {
        $product = $demanded instanceof Demand ? $demanded->getProduct() : $demanded;

        foreach ($this->getDemands() as $demand) /** @var Demand $demand */ {
            if ($demand->representsProduct($product)) {
                return $demand;
            }
        }

        return Demand::of($product);
    }

    /**
     * Demand the product.
     *
     * @param Product $product the demanded product.
     * @param int $number the number of units of the product to demand.
     * @return Demand the affected demand.
     */
    public function demand(Product $product, int $number = 1): Demand
    {
        $demand = $this->getDemandOf($product);

        $demand->more($number);

        $this->addDemand($demand);

        return $demand;
    }

    /**
     * Pick up the product.
     *
     * @param Product $product the demanded product.
     * @param int $number the number of units of the product to pick up.
     * @return Demand the affected demand.
     */
    public function remove(Product $product, int $number = 1): Demand
    {
        $demand = $this->getDemandOf($product);

        $demand->less($number);

        return $demand;
    }

    /**
     * Update the current set of demands preserving the useful demands.
     *
     * @return Collection<Demand> the useless demands. They should be removed.
     */
    public function filterUselessDemands(): Collection
    {
        [$useless, $allowed] = $this->getDemands()->partition(fn($_, Demand $demand) => $demand->isUseless());

        $this->demands = $allowed;

        return $useless;
    }

    /**
     * Check if this shopping cart is owned by the provided user.
     *
     * @param UserInterface|string $user the user or his/her email.
     */
    #[Pure] public function belongsTo(UserInterface|string $user): bool
    {
        $email = $user instanceof UserInterface ? $user->getUserIdentifier() : $user;

        return $this->getOwner()?->getUserIdentifier() === $email;
    }

    public function addDemand(Demand $demand): self
    {
        if (!$this->getDemands()->contains($demand)) {
            $this->getDemands()->add($demand);
            $demand->setCart($this);
        }

        return $this;
    }

    public function removeDemand(Demand $demand): self
    {
        if ($this->getDemands()->removeElement($demand)) {
            // set the owning side to null (unless already changed)
            if ($demand->getCart() === $this) {
                $demand->setCart(null);
            }
        }

        return $this;
    }
}
