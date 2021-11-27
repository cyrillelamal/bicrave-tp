<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\ProductFactory;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const NB_PER_CATEGORY = 50;

    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            $products = ProductFactory::makeProducts($category, self::NB_PER_CATEGORY);

            foreach ($products as $product) {
                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
