<?php

namespace App\Tests\Controller;

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

    public function testAnonymousUsersCanSeeTheirCart(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, self::INDEX);

        $this->assertResponseIsSuccessful();
    }

    public function testUsersCanPutProductsInCart(): void
    {
        $client = static::createClient();

        $product = $this->getRandomProduct();
        $client->request(Request::METHOD_GET, $this->getUri($product));

        $code = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $code);
    }

    public function testUsersCanPickUpProductsFromCart(): void
    {
        $client = static::createClient();

        $product = $this->getRandomProduct();
        $client->request(Request::METHOD_GET, $this->getUri($product));

        $code = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $code);
    }

    #[Pure] private function getUri(Product|int|null $product = null): string
    {
        if (null === $product) {
            return self::INDEX;
        }

        $id = $product instanceof Product ? $product->getId() : $product;

        return sprintf(self::PUT_PRODUCT, $id);
    }

    private function getRandomProduct(): Product
    {
        $repository = $this->getEntityManager()->getRepository(Product::class);

        $products = $repository->findAll();

        return $products[array_rand($products)];
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
