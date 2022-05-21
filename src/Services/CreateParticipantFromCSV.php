<?php

namespace App\Services;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;

class CreateParticipantFromCSV
{
    private string $fileLocation;
    private string $fileName;
    private ParticipantRepository $participantRepository;
    private CampusRepository $campusRepository;

    public function __construct($fileLocation, $fileName, ParticipantRepository $participantRepository, CampusRepository $campusRepository)
    {
        $this->fileLocation = $fileLocation;
        $this->fileName = $fileName;
        $this->participantRepository = $participantRepository;
        $this->campusRepository = $campusRepository;
    }

    public function importUser(){

        if (($handle = fopen($this->fileLocation . '/' . $this->fileName, "r")) !== FALSE) {
            $row = 1;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row == 1) {
                    $row++;
                } else if ($data[0] != '') {
                    $participantCSV = new Participant();

                    $participantCSV->setCampus($this->campusRepository->findOneBy(array('nom' => $data[0])));
                    $participantCSV->setPseudo($data[1]);
                    $participantCSV->setRoles($data[7] == 'true' ? ["ROLE_ADMIN"] : ["ROLE_USER"]);
                    $participantCSV->setPassword(password_hash($data[2], PASSWORD_BCRYPT));
                    $participantCSV->setPrenom($data[3]);
                    $participantCSV->setNom($data[4]);
                    $participantCSV->setTelephone($data[5]);
                    $participantCSV->setEmail($data[6]);
                    $participantCSV->setAdministrateur($data[7] == 'true');
                    $participantCSV->setActif(true);
                    $participantCSV->setPhoto('default.png');

                    $this->participantRepository->add($participantCSV, true);
                }

            }

            fclose($handle);

        }

    }
}