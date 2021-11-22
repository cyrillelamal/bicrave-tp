<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\DemandFactory;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DemandFixtures extends Fixture implements DependentFixtureInterface
{
    private ProductRepository $productRepository;
    private CartRepository $cartRepository;

    public function __construct(
        ProductRepository $productRepository,
        CartRepository    $cartRepository,
    )
    {
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $carts = $this->cartRepository->findAll();
        $products = $this->productRepository->findAll();

        foreach ($carts as $cart) {
            shuffle($products);
            $selection = array_slice($products, 1, 5);

            foreach ($selection as $product) {
                $demand = DemandFactory::makeDemand($product, $cart);

                $manager->persist($demand);
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
            CartFixtures::class,
        ];
    }
}
