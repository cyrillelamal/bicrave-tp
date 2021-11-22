<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\CategoryFactory;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const NB_TOP_LEVEL = 2;
    public const NB_CHILDREN_RANGE = [1, 3];
    public const DEPTH = 3;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $walker = function (?Category $parent = null, int $count = 3, int $depth = 0) use ($manager, &$walker) {
            if ($depth < 0) return;

            $children = CategoryFactory::makeCategories($parent, $count);

            foreach ($children as $child) {
                $manager->persist($child);
                $walker($child, rand(...self::NB_CHILDREN_RANGE), $depth - 1);
            }
        };

        $walker(null, self::NB_TOP_LEVEL, self::DEPTH);

        $manager->flush();
    }
}
