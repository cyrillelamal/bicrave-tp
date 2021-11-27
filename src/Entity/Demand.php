<?php

namespace App\Entity;

use App\Entity\Accessor\DemandGetters;
use App\Entity\Accessor\DemandSetters;
use App\Repository\DemandRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @OA\Schema()
 */
#[ORM\Entity(repositoryClass: DemandRepository::class)]
class Demand
{
    use DemandGetters;
    use DemandSetters;

    /**
     * @OA\Property(property="id", @OA\Schema(type="integer"))
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([Cart::READ])]
    private ?int $id = null;

    /**
     * @OA\Property(property="product", @OA\Schema(type="object"))
     */
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Cart::READ])]
    private ?Product $product = null;

    /**
     * @OA\Property(property="number", @OA\Schema(type="integer"))
     */
    #[ORM\Column(type: 'integer')]
    #[Groups([Cart::READ])]
    private ?int $number = 0;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'demands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt;

    /**
     * Make a new demand.
     * Simple factory.
     *
     * @param Product $product the demanded product.
     * @param int $number the number of units of the demanded product.
     * @return static
     */
    public static function of(Product $product, int $number = 0): self
    {
        $demand = new self();

        $demand->setProduct($product);
        $demand->setNumber($number);

        return $demand;
    }

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Get the demand total cost.
     *
     * @return int cents.
     */
    #[Pure] public function getTotal(): int
    {
        return $this->getNumber() * $this->getProduct()?->getCost() ?? 0;
    }

    /**
     * Get the final demand price.
     *
     * @return float not formatted application currency value.
     */
    #[Pure] public function getPrice(): float
    {
        return $this->getTotal() / 100;
    }

    /**
     * Check if this demand represents the provided product.
     */
    #[Pure] public function representsProduct(?Product $product): bool
    {
        return $this->getProduct() === $product
            || $this->getProduct()?->getId() === $product?->getId();
    }

    /**
     * Check if this demand is useless.
     * Useless demand should be removed.
     */
    #[Pure] public function isUseless(): bool
    {
        return $this->number < 1 || null === $this?->getProduct()?->getId();
    }

    /**
     * Check if this demand cannot be realized because the demanded product is out of stock.
     */
    #[Pure] public function isOutOfStock(): bool
    {
        return $this->getProduct()->isOutOfStock();
    }

    /**
     * Demand more units of the demanded product.
     *
     * @param int $number how many units of the product to add.
     * @return int the new number of demanded product.
     */
    public function more(int $number): int
    {
        return $this->number += abs($number);
    }

    /**
     * Pick up some amount of units of the demanded product.
     *
     * @param int $number how many units of the product to pick up.
     * @return int the new number of demanded product.
     */
    public function less(int $number): int
    {
        return $this->number -= abs($number);
    }

    /**
     * Check if this demand is less valuable than the other.
     */
    #[Pure] public function isLessValuableThan(Demand $other): bool
    {
        return $this->getTotal() < $other->getTotal();
    }
}
