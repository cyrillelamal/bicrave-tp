<?php

namespace App\DataFixtures\Factory;

use App\Entity\Category;

class CategoryFactory
{
    use Faker;

    /**
     * Simple factory for category entities.
     *
     * @param Category|null $parent the parent category.
     * @param int $count how many child categories to make.
     * @return Category[]
     */
    public static function makeCategories(?Category $parent = null, int $count = 3): array
    {
        return array_map(function () use ($parent) {
            $category = new Category();

            $category->setName(self::getFaker()->unique()->words(rand(1, 4), true));
            $category->setParent($parent);

            return $category;
        }, array_fill(0, $count, null));
    }
}
