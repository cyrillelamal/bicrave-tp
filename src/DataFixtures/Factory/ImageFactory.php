<?php

namespace App\DataFixtures\Factory;

use App\Entity\Image;
use App\Entity\Product;
use DateTimeImmutable;
use DateTimeInterface;

class ImageFactory
{
    use Faker;

    /**
     * Simple factory for product entities.
     *
     * @param Product $product the related product.
     * @param int $count how many images to create.
     * @return Image[]
     */
    public static function makeImages(Product $product, int $count = 1): array
    {
        return array_map(function () use ($product) {
            $image = new Image();

            $image->setName(self::getFaker()->imageUrl());
            $image->setUploadedAt(self::getDateTimeAfter($product->getCreatedAt()));
            $image->setProduct($product);

            return $image;
        }, array_fill(0, $count, null));
    }

    protected static function getDateTimeAfter(DateTimeInterface $dateTime): DateTimeInterface
    {
        return DateTimeImmutable::createFromInterface(
            self::getFaker()->dateTimeBetween($dateTime->format(DateTimeInterface::ATOM))
        );
    }
}