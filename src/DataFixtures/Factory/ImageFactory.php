<?php

namespace App\DataFixtures\Factory;

use App\Entity\Image;
use App\Entity\Product;
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
            $image->setUploadedAt(self::getFaker()->dateTimeBetween($product->getCreatedAt()->format(DateTimeInterface::ATOM)));
            $image->setProduct($product);

            return $image;
        }, array_fill(0, $count, null));
    }
}