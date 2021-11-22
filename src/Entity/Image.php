<?php

namespace App\Entity;

use App\Entity\Accessor\ImageGetters;
use App\Entity\Accessor\ImageSetters;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable()
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    use ImageGetters;
    use ImageSetters;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeInterface $uploadedAt = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    /**
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="name")
     */
    private ?File $file = null;

    #[Pure] public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;

        if ($file) {
            $this->setUploadedAt(new DateTimeImmutable());
        }
    }
}
