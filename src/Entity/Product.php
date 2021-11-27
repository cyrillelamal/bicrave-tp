<?php

namespace App\Entity;

use App\Entity\Accessor\ProductGetters;
use App\Entity\Accessor\ProductSetters;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @OA\Schema()
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Orm\Index(columns: ['created_at'], name: 'novelties_index')]
class Product
{
    use ProductGetters;
    use ProductSetters;

    /**
     * @OA\Property(property="id", @OA\Schema(type="integer"))
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([Cart::READ])]
    private ?int $id = null;

    /**
     * @OA\Property(property="name", @OA\Schema(type="string"))
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([Cart::READ])]
    private ?string $name = null;

    #[ORM\Column(type: 'integer')]
    private ?int $cost = null;

    #[ORM\Column(type: 'integer')]
    private ?int $rest = 0;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private ?DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class)]
    private Collection $images;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category;

    #[ORM\Column(type: 'float')]
    private ?float $popularity = 0.0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = '';

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->images = new ArrayCollection();
    }

    /**
     * Get the final product price.
     *
     * @return float the application's currency value: not cents.
     */
    #[Pure] public function getPrice(): float
    {
        return $this->getCost() / 100;
    }

    /**
     * Check if this product is not available in stock.
     */
    #[Pure] public function isOutOfStock(): bool
    {
        return $this->getRest() < 1;
    }

    public function addImage(Image $image): self
    {
        if (!$this->getImages()->contains($image)) {
            $this->getImages()->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->getImages()->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->getName();
    }

    public function __serialize(): array
    {
        return array_merge(
            get_object_vars($this),
            [
                'images' => $this->getImages()->toArray(),
            ]
        );
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $property => $value) {
            if ('images' === $property) {
                $value = new ArrayCollection($value);
            }

            $this->$property = $value;
        }
    }

    /**
     * Reserve the product in the stock.
     *
     * @param int $number how many units of the product to reserve.
     * @return int the new number of units of the product in the stock.
     */
    public function reserve(int $number): int
    {
        return $this->rest -= abs($number);
    }
}
