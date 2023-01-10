<?php

namespace App\Tests;

use App\Entity\Mark;
use App\Entity\Recette;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecetteTest extends KernelTestCase
{
    public function getEntity(): Recette
    {$now = new \DateTimeImmutable();
        return (new Recette())
            ->setName('Tarte aux pommes')
            ->setDescription('Une tarte traditionnelle aux pommes.')
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

    }

    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $recette = $this->getEntity();

        $errors = $container->get('validator')->validate($recette);
        $this->assertCount(0, $errors);

    }

    public function testNameCannotBeEmpty()
    {
        self::bootKernel();
        $container = static::getContainer();

        $recette = $this->getEntity();
        $recette->setName('');

        $errors = $container->get('validator')->validate($recette);
        $this->assertCount(0, $errors);
    }

    public function testAverage()
    {

        $container = static::getContainer();

        $recette = $this->getEntity();

        $user = $container->get('doctrine.orm.entity_manager')->find(User::class, 1);

        for ($i = 0; $i < 5; $i++) {
            $mark = new Mark();
            $mark->setMark(5)
                ->setUser($user)
                ->setRecette($recette);
            $recette->addMark($mark);
        }
        $this->assertTrue(5.0 ===$recette->getAverage());
    }
}
