<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CartFixtures extends Fixture implements DependentFixtureInterface
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
        $users = array_filter($users, fn(User $user) => $user->hasRole(Role::CUSTOMER));

        foreach ($users as $user) {
            $cart = new Cart();

            $cart->setOwner($user);

            $manager->persist($cart);
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
