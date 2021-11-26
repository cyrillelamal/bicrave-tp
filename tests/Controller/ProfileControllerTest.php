<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileControllerTest extends WebTestCase
{
    public const INDEX = '/profiles';

    public function testUsersCanAccessTheirProfile(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::INDEX);

        $code = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $code);
    }

    public function testAnonymousUseHasNoProfile(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::INDEX);

        $this->assertResponseRedirects();
    }

    public function testCustomerCanAccessHisProfile(): void
    {
        $client = static::createClient();

        $user = $this->getRandomUser(fn(User $user) => $user->hasRole(Role::CUSTOMER));

        $client->loginUser($user)->request(Request::METHOD_GET, self::INDEX);

        $this->assertResponseIsSuccessful();
    }

    public function testEmployeesHasNoProfile(): void
    {
        $client = static::createClient();

        $user = $this->getRandomUser(fn(User $user) => $user->hasRole(Role::CONTENT_MANAGER));

        $client->loginUser($user)->request(Request::METHOD_GET, self::INDEX);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    private function getRandomUser(?callable $filter = null): User
    {
        $repository = self::getContainer()->get(EntityManagerInterface::class)->getRepository(User::class);

        $users = $repository->findAll();

        if (null !== $filter) {
            $users = array_filter($users, $filter);
        }

        return $users[array_rand($users)];
    }
}
