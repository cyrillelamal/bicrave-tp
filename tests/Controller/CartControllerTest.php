<?php

namespace App\Tests\Controller;

use App\Entity\Cart;
use App\Entity\Demand;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartControllerTest extends WebTestCase
{
    public const INDEX = '/carts';
    public const PUT_PRODUCT = '/carts/%d'; // same as pick up product

    /**
     * @test
     */
    public function anonymous_user_can_see_his_cart(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, self::INDEX);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function user_can_put_products_in_the_cart(): void
    {
        $client = static::createClient();

        $product = $this->getRandomProduct();
        $client->request(Request::METHOD_GET, $this->getUri($product));

        $code = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $code);
    }

    /**
     * @test
     */
    public function user_can_pick_up_products_from_the_cart(): void
    {
        $client = static::createClient();

        $product = $this->getRandomProduct();
        $client->request(Request::METHOD_GET, $this->getUri($product));

        $code = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $code);
    }

    /**
     * @test
     */
    public function it_lists_products_of_user_cart(): void
    {
        $client = self::createClient();

        $cart = $this->getRandomUserCart();
        $user = $cart->getOwner();

        $client->loginUser($user)->request(Request::METHOD_GET, self::INDEX);

        $html = $client->getResponse()->getContent();
        foreach ($cart->getDemands() as $demand) /** @var Demand $demand */ {
            $this->assertStringContainsString($demand->getProduct()->getName(), $html);
        }
    }

    #[Pure] private function getUri(Product|int|null $product = null): string
    {
        if (null === $product) {
            return self::INDEX;
        }

        $id = $product instanceof Product ? $product->getId() : $product;

        return sprintf(self::PUT_PRODUCT, $id);
    }

    /**
     * @param int $count
     * @return Product[]
     */
    private function getRandomProducts(int $count = 1): array
    {
        $repository = $this->getEntityManager()->getRepository(Product::class);

        $products = $repository->findAll();
        shuffle($products);

        return array_slice($products, 0, $count);
    }

    private function getRandomProduct(): Product
    {
        $repository = $this->getEntityManager()->getRepository(Product::class);

        $products = $repository->findAll();

        return $products[array_rand($products)];
    }

    private function getRandomUserCart(): Cart
    {
        $repository = $this->getEntityManager()->getRepository(Cart::class);

        $carts = $repository->findAll();

        return $carts[array_rand($carts)];
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
