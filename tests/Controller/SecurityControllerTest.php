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
    public const REGISTER = '/register';

    /**
     * @test
     */
    public function anonymous_user_can_log_in(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::LOGIN);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function authenticated_user_cannot_log_in(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getRandomUser())->request(Request::METHOD_GET, self::LOGIN);

        $this->assertResponseRedirects();
    }

    /**
     * @test
     */
    public function log_out_action_requires_csrf_token(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getRandomUser())->request(Request::METHOD_GET, self::LOGOUT);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function users_can_sign_up(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, self::REGISTER);

        $this->assertResponseIsSuccessful();
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
