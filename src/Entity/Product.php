<?php

namespace App\Entity;

use App\Entity\Accessor\ProductGetters;
use App\Entity\Accessor\ProductSetters;
use App\Repository\ProductRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use ProductGetters;
    use ProductSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'integer')]
    private ?int $cost = null;

    #[ORM\Column(type: 'integer')]
    private ?int $rest = 0;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class)]
    private Collection $images;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EAGER', inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category;

    #[ORM\Column(type: 'float')]
    private ?float $popularity = 0.0;

    #[Pure] public function __construct()
    {
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
}
