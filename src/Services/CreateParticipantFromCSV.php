<?php

namespace App\Services;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreateParticipantFromCSV
{
    private string $fileLocation;
    private string $fileName;
    private CampusRepository $campusRepository;
    private EntityManagerInterface $entityManager;


    public function __construct($fileLocation, $fileName, CampusRepository $campusRepository, EntityManagerInterface $entityManager)
    {
        $this->fileLocation = $fileLocation;
        $this->fileName = $fileName;
        $this->campusRepository = $campusRepository;
        $this->entityManager = $entityManager;
    }

    public function importUser(){

        if (($handle = fopen($this->fileLocation . '/' . $this->fileName, "r")) !== FALSE) {
            $row = 1;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row == 1) {
                    $row++;
                } else if ($data[0] != '') {
                    $participant = new Participant();

                    $participant->setCampus($this->campusRepository->findOneBy(array('nom' => $data[0])));
                    $participant->setPseudo($data[1]);
                    $participant->setRoles($data[7] == 'true' ? ["ROLE_ADMIN"] : ["ROLE_USER"]);
                    $participant->setPassword(password_hash($data[2], PASSWORD_BCRYPT));
                    $participant->setPrenom($data[3]);
                    $participant->setNom($data[4]);
                    $participant->setTelephone($data[5]);
                    $participant->setEmail($data[6]);
                    $participant->setAdministrateur($data[7] == 'true');
                    $participant->setActif(true);
                    $participant->setPhoto('default.png');

                    $this->entityManager->persist($participant);
                }

            }
            $this->entityManager->flush();
            fclose($handle);

        }

    }
}