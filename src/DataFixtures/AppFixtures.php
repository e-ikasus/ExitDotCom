<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
	const NBR_CAMPUS = 10;
	const NBR_VILLES = 30;
	const NBR_LIEUX = 10;
	const NBR_PARTICIPANTS = 50;
	const NBR_SORTIES = 70;
	const NBR_ETATS = 6;

	const MAX_PARTICIPANTS_PAR_SORTIE = 10;

	public function load(ObjectManager $manager): void
	{
		$faker = Faker\Factory::create('fr_FR');

		/********************/
		/* Liste des villes */
		/********************/

		$villes = array();

		for ($i = 0; $i < self::NBR_VILLES; $i++)
		{
			$villes[$i] = new Ville();
			$villes[$i]->setNom($faker->city());
			$villes[$i]->setCodePostal(substr(str_replace(" ", "", $faker->postcode()), 0, 5));

			$manager->persist($villes[$i]);
		}

		$manager->flush();

		/*******************/
		/* Liste des lieux */
		/*******************/

		$lieux = array();

		for ($i = 0; $i < self::NBR_LIEUX; $i++)
		{
			$lieux[$i] = new Lieu();
			$lieux[$i]->setNom($faker->userName());
			$lieux[$i]->setRue($faker->streetAddress());
			$lieux[$i]->setLatitude($faker->latitude());
			$lieux[$i]->setLongitude($faker->longitude());

			$villes[rand(0, self::NBR_VILLES - 1)]->addLieux($lieux[$i]);

			$manager->persist($lieux[$i]);
		}

		$manager->flush();

		/********************/
		/* Liste des campus */
		/********************/

		$campus = array();

		for ($i = 0; $i < self::NBR_CAMPUS; $i++)
		{
			$campus[$i] = new Campus();
			$campus[$i]->setNom($faker->company());

			$manager->persist($campus[$i]);
		}

		$manager->flush();

		/*******************/
		/* Liste des états */
		/*******************/

		$etats = array();

		$etats[0] = (new Etat())->setLibelle("créée");
		$etats[1] = (new Etat())->setLibelle("ouverte");
		$etats[2] = (new Etat())->setLibelle("clôturée");
		$etats[3] = (new Etat())->setLibelle("activité en cours");
		$etats[4] = (new Etat())->setLibelle("passée");
		$etats[5] = (new Etat())->setLibelle("annulée");

		for ($i = 0; $i < 6; $i++) $manager->persist($etats[$i]);

		$manager->flush();

		/********************/
		/* Ajouter un Admin */
		/********************/

		$adminUser = new Participant();
		$adminUser->setRoles(["ROLE_ADMIN"]);
		$adminUser->setNom("admin");
		$adminUser->setPrenom("admin");
		$adminUser->setPseudo("admin");
		$adminUser->setPassword(password_hash('admin', PASSWORD_BCRYPT));
		$adminUser->setEmail("admin@admin.fr");
		$adminUser->setTelephone("0601020304");
		$adminUser->setAdministrateur(true);
		$adminUser->setActif(true);

		$campus[rand(0, self::NBR_CAMPUS - 1)]->addParticipant($adminUser);

		$manager->persist($adminUser);
		$manager->flush();

		/**************************/
		/* Liste des participants */
		/**************************/

		$participants = array();

		for ($i = 0; $i < self::NBR_PARTICIPANTS; $i++)
		{
			$participants[$i] = new Participant();

			$participants[$i]->setRoles(["ROLE_USER"]);
			$participants[$i]->setNom($faker->lastName());
			$participants[$i]->setPrenom($faker->firstName());
			$participants[$i]->setPseudo($faker->userName());
			$participants[$i]->setPassword(password_hash('user', PASSWORD_BCRYPT));
			$participants[$i]->setEmail($faker->email());
			$participants[$i]->setTelephone($faker->phoneNumber());
			$participants[$i]->setAdministrateur(false);
			$participants[$i]->setActif(true);

			$campus[rand(0, self::NBR_CAMPUS - 1)]->addParticipant($participants[$i]);

			$manager->persist($participants[$i]);
		}

		$manager->flush();

		/*********************/
		/* Liste des sorties */
		/*********************/

		$sorties = array();

		for ($i = 0; $i < self::NBR_SORTIES; $i++)
		{
			$sorties[$i] = new Sortie();
			$sorties[$i]->setnom($faker->colorName());
			$sorties[$i]->setDuree(rand(1, 23));

			$cloture = $faker->dateTime();
			$sortie = $cloture->add(new \DateInterval("P1M"));

			$sorties[$i]->setDateHeureDebut($sortie);
			$sorties[$i]->setDateLimiteInscription($cloture);

			$sorties[$i]->setNbInscriptionsMax(rand(1, 20));
			$sorties[$i]->setInfosSortie($faker->text());

			$campus[rand(0, self::NBR_CAMPUS - 1)]->addSortieOrganisee($sorties[$i]);

			$lieux[rand(0, self::NBR_LIEUX - 1)]->addSorty($sorties[$i]);

			$etats[rand(0, self::NBR_ETATS - 1)]->addSorty($sorties[$i]);

			$participants[rand(0, self::NBR_PARTICIPANTS - 1)]->addSortiesOrganisee($sorties[$i]);

			for ($j = 0; $j < rand(0, self::MAX_PARTICIPANTS_PAR_SORTIE); $j++)
			{
				$part = $participants[rand(0, self::NBR_PARTICIPANTS - 1)];

				while ($sorties[$i]->getParticipants()->contains($part)) $part = $participants[rand(0, self::NBR_PARTICIPANTS - 1)];

				$sorties[$i]->addParticipant($part);
			}

			$manager->persist($sorties[$i]);
		}

		$manager->flush();
	}
}
