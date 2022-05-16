<?php

namespace App\DataFixtures;

use App\Repository\CampusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class Participant extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $campusRepository= CampusRepository();
        $faker = Faker\Factory::create('fr_FR');
        // on crée 30 participants "aléatoires" en français
        $participants = Array();
        for ($i = 0; $i < 30; $i++) {
            $campus= $campusRepository->find($faker->numberBetween($min=1, $max=10));
            $participants[$i] = new \App\Entity\Participant();
            $participants[$i]->setCampus($campus);
            $participants[$i]->setNom($faker->lastName);
            $participants[$i]->setPrenom($faker->firstName);
            $participants[$i]->setPseudo($faker->name);
            $participants[$i]->setPassword($faker->password);
            $participants[$i]->setActif(true);
            $participants[$i]->setAdministrateur(true);
            $participants[$i]->setTelephone($faker->phoneNumber);
            $participants[$i]->setEmail($faker->email);
            $manager->persist($participants[$i]);

        $manager->flush();
    }
}}
