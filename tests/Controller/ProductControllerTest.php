<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    public const READ = '/products/%d';

    /**
     * @test
     */
    public function users_can_see_product_information(): void
    {
        $client = static::createClient();

        $product = $this->getRandomProduct();
        $uri = $this->getUri($product);

        $client->request(Request::METHOD_GET, $uri);

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains($product->getName());
    }

    /**
     * @test
     */
    public function users_cannot_see_not_existing_products(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, $this->getUri(-1));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Pure] private function getUri(Product|int $product): string
    {
        $id = $product instanceof Product ? $product->getId() : $product;

        return sprintf(self::READ, $id);
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
