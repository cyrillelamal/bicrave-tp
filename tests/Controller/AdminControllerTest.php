<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase
{
    public const URI = '/admin';

    public function testCustomersHaveNoAccessToAdminDashboard(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, self::URI);
        $this->assertResponseRedirects();

        $customer = $this->getRandomUser(fn(User $user) => $user->hasRole(Role::CUSTOMER));
        $client->loginUser($customer)->request(Request::METHOD_GET, self::URI);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testContentManagersHaveAccessToAdminDashboard(): void
    {
        $client = static::createClient();

        $manager = $this->getRandomUser(fn(User $user) => $user->hasRole(Role::CONTENT_MANAGER));
        $client->loginUser($manager)->request(Request::METHOD_GET, self::URI);
        $this->assertResponseIsSuccessful();
    }

    public function getRandomUser(?callable $filter = null): User
    {
        $repository = $this->getEntityManager()->getRepository(User::class);
        $users = $repository->findAll();
        shuffle($users);

        if (null !== $filter) {
            $users = array_filter($users, $filter);
        }

        return $users[array_rand($users)];
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
