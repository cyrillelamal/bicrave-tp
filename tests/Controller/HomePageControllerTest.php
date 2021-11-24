<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class HomePageControllerTest extends WebTestCase
{
    const URI = '/';

    public function testEverybodyCanAccessTheHomePage(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::URI);

        $this->assertResponseIsSuccessful();
    }
}
