<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\ReservationFactory;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;

    public function __construct(
        OrderRepository   $orderRepository,
        ProductRepository $productRepository,
    )
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $orders = $this->orderRepository->findAll();
        $products = $this->productRepository->findAll();

        foreach ($orders as $order) {
            shuffle($products);
            $selection = array_slice($products, 1, rand(2, 4));

            foreach ($selection as $product) {
                $reservation = ReservationFactory::makeReservation($product, $order);

                $manager->persist($reservation);
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
            OrderFixtures::class,
            ProductFixtures::class,
        ];
    }
}
