<?php

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends WebTestCase
{
    public const READ = '/orders/%d';

    /**
     * @test
     */
    public function users_can_see_their_orders(): void
    {
        $client = static::createClient();

        $order = $this->getRandomOrder();

        $client->request(Request::METHOD_GET, $this->getUri($order));

        $code = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $code);
    }

    /**
     * @test
     */
    public function anonymous_user_cannot_see_any_order(): void
    {
        $client = static::createClient();

        $order = $this->getRandomOrder();

        $client->request(Request::METHOD_GET, $this->getUri($order));
        $this->assertResponseRedirects();
    }

    /**
     * @test
     */
    public function customer_can_see_his_orders(): void
    {
        $client = static::createClient();

        $order = $this->getRandomOrder();

        $client->loginUser($order->getCustomer())->request(Request::METHOD_GET, $this->getUri($order));
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function bob_cannot_see_orders_of_mary(): void
    {
        $client = static::createClient();

        [$bob, $mary] = $this->getRandomCustomers(2);
        $order = $mary->getOrders()->first();

        $client->loginUser($bob)->request(Request::METHOD_GET, $this->getUri($order));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    #[Pure] private function getUri(Order|int $order): string
    {
        $id = $order instanceof Order ? $order->getId() : $order;

        return sprintf(self::READ, $id);
    }

    private function getRandomOrder(): Order
    {
        $repository = self::getContainer()->get(EntityManagerInterface::class)->getRepository(Order::class);

        $orders = $repository->findAll();

        return $orders[array_rand($orders)];
    }

    /**
     * @param int $count
     * @return User[]
     */
    private function getRandomCustomers(int $count = 1): array
    {
        $repository = self::getContainer()->get(EntityManagerInterface::class)->getRepository(User::class);

        $users = $repository->findAll();

        $customers = array_filter($users, fn(User $user) => $user->hasRole(Role::CUSTOMER));
        shuffle($customers);

        return array_slice($customers, 0, $count);
    }
}
