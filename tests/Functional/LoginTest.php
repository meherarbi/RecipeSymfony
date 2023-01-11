<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@meher.com",
            "_password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('app_home');
    }



    public function testIfLoginFailedWhenPasswordIsWrong(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@meher.com",
            "_password" => "password_"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('app_login');

        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials.");
    }


}
