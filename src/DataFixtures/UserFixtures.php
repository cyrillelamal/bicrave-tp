<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const NB_CUSTOMERS = 12;
    public const NB_CONTENT_MANAGERS = 4;

    public const PASSWORD = 'password';

    private UserPasswordHasherInterface $hasher;
    private Generator $faker;

    private ?string $hash = null;

    public function __construct(
        UserPasswordHasherInterface $hasher,
    )
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $users = array_merge(
            array_map(function (User $user) {
                $user->addRole(Role::CONTENT_MANAGER);
                return $user;
            }, $this->makeUsers(self::NB_CONTENT_MANAGERS)),

            array_map(function (User $user) {
                $user->addRole(Role::CUSTOMER);
                return $user;
            }, $this->makeUsers(self::NB_CUSTOMERS)),
        );

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return User[]
     */
    protected function makeUsers(int $count = 1): array
    {
        return array_map(function () {
            $user = new User();

            $user->setEmail($this->faker->unique()->email());
            $user->setPassword($this->getHash($user));

            return $user;
        }, array_fill(0, $count, null));
    }

    protected function getHash(User $user): string
    {
        return $this->hash = $this->hash ?? $this->hasher->hashPassword($user, self::PASSWORD);
    }
}
