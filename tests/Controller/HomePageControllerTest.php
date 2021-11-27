<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class HomePageControllerTest extends WebTestCase
{
    const URI = '/';

    /**
     * @test
     */
    public function everybody_can_access_the_home_page(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::URI);

        $this->assertResponseIsSuccessful();
    }
}
