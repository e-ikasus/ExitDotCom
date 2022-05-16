<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class Campus extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        // on crée 10 campus "aléatoires" en français
        $campus = Array();
        for ($i = 0; $i < 10; $i++) {
            $campus[$i] = new \App\Entity\Campus();
            $campus[$i]->setNom($faker->city);

            $manager->persist($campus[$i]);
        }


        $manager->flush();
    }
}
