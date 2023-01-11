<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class homePageTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertResponseIsSuccessful();

        $button = $crawler->filter('.btn.btn-primary.btn-lgz');
        $this->assertEquals(1, count($button));

        


        $this->assertSelectorTextContains('h1', 'Bienvenue sur My Recipes!');
    }
}
