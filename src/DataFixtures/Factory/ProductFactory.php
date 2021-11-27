<?php

namespace App\DataFixtures\Factory;

use App\Entity\Category;
use App\Entity\Product;

class ProductFactory
{
    use Faker;

    /**
     * Simple factory for product entities.
     *
     * @param Category $category the related category.
     * @param int $count how many products to make.
     * @return Product[]
     */
    public static function makeProducts(Category $category, int $count = 1): array
    {
        return array_map(function () use ($category) {
            $product = new Product();

            $product->setName(self::getFaker()->words(rand(2, 5), true));
            $product->setCost(rand(99, 99_99));
            $product->setRest(rand(100, 1_000));
            $product->setCreatedAt(self::getFaker()->dateTime());
            $product->setUpdatedAt(self::getFaker()->dateTimeBetween($product->getCreatedAt()->format(DATE_ATOM)));
            $product->setCategory($category);
            $product->setPopularity(lcg_value());
            $product->setDescription(self::getFaker()->sentences(rand(1, 3), true));

            return $product;
        }, array_fill(0, $count, null));
    }
}
