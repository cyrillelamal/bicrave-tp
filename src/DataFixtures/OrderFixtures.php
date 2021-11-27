<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\OrderFactory;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        $customers = array_filter($users, fn(User $user) => $user->hasRole(Role::CUSTOMER));

        foreach ($customers as $customer) {
            $orders = OrderFactory::makeOrders($customer, rand(1, 4));

            foreach ($orders as $order) {
                $manager->persist($order);
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
            UserFixtures::class,
        ];
    }
}
