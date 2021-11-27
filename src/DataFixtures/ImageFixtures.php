<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\ImageFactory;
use App\Repository\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    private ProductRepository $productRepository;

    public function __construct(
        ProductRepository $productRepository,
    )
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            $images = ImageFactory::makeImages($product, rand(2, 4));

            foreach ($images as $image) {
                $manager->persist($image);
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
            ProductFixtures::class,
        ];
    }
}
