<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
   
    public function testIfSubmitContactFormIsSuccessfull(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        // Récupérer le formulaire
        $submitButton = $crawler->selectButton('Soumettre ma demande');
        $form = $submitButton->form();

        $form["contact[fullName]"] = "Meher Arbi";
        $form["contact[email]"] = "meher@arby.com";
        $form["contact[subject]"] = "Test";
        $form["contact[message]"] = "Test";

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier le statut HTTP
        $this->assertResponseStatusCodeSame(200, "succes");

        $this->assertResponseIsSuccessful();

    }
}
