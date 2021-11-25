<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    public const INDEX = '/categories';
    public const READ = '/categories/%d';

    public function testUsersCanSeeTheListOfAllCategories(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, self::INDEX);

        $this->assertResponseIsSuccessful();

        $body = $crawler->text('body');
        foreach ($this->getAllCategories() as $category) {
            $this->assertStringContainsString($category->getName(), $body);
        }
    }

    public function testUsersCanSeeTheSpecifiedCategory(): void
    {
        $client = static::createClient();

        $category = $this->getRandomCategory();

        $client->request(Request::METHOD_GET, $this->getReadUri($category));

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains($category->getName());
    }

    public function testUsersCannotSeeNotExistingCategories(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, $this->getReadUri(-1));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Pure] private function getReadUri(Category|int $category): string
    {
        $id = $category instanceof Category ? $category->getId() : $category;

        return sprintf(self::READ, $id);
    }

    private function getRandomCategory(): Category
    {
        $categories = $this->getAllCategories();

        return $categories[array_rand($categories)];
    }

    /**
     * @return Category[]
     */
    private function getAllCategories(): array
    {
        $repository = $this->getEntityManager()->getRepository(Category::class);

        return $repository->findAll();
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
