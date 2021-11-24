<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public const LOGIN = '/login';
    public const LOGOUT = '/logout';

    public function testAnonymousUsersCanLogIn(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::LOGIN);

        $this->assertResponseIsSuccessful();
    }

    public function testAuthenticatedUsersCannotLogIn(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getRandomUser())->request(Request::METHOD_GET, self::LOGIN);

        $this->assertResponseRedirects();
    }

    public function testLogOutActionRequiresCsrfToken(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getRandomUser())->request(Request::METHOD_GET, self::LOGOUT);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    private function getRandomUser(): User
    {
        $repository = $this->getEntityManager()->getRepository(User::class);

        $users = $repository->findAll();

        return $users[array_rand($users)];
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
