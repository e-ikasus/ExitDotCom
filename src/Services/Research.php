<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Form\RechercheSortiesType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class Research extends AbstractController
{
    private $campus;

    private $searchOutingName;

    private $dateOutingStart;

    private $dateOutingEnd;

    private $sortiesOrganisateur;
    private $sortiesNonInscrit;
    private $sortiesInscrit;
    private $sortiesPassees;

    public function __construct($campus,
                                $searchOutingName,
                                $dateOutingStart,
                                $dateOutingEnd,
                                $sortiesOrganisateur,
                                $sortiesNonInscrit,
                                $sortiesInscrit,
                                $sortiesPassees)
    {
        $this->campus = $campus;
        $this->searchOutingName = $searchOutingName;
        $this->dateOutingStart = $dateOutingStart;
        $this->dateOutingEnd = $dateOutingEnd;
        $this->sortiesOrganisateur = $sortiesOrganisateur;
        $this->sortiesNonInscrit = $sortiesNonInscrit;
        $this->sortiesInscrit = $sortiesInscrit;
        $this->sortiesPassees = $sortiesPassees;
    }

    public function research(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(RechercheSortiesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campus = $form['Campus']->getData();
            $searchOutingName = $form['SearchOutingName']->getData();
            $dateOutingStart = $form['DateOutingStart']->getData();
            $dateOutingEnd = $form['DateOutingEnd']->getData();
            $sortiesOrganisateur = $form['sorties-organisateur']->getData();
            $sortiesNonInscrit = $form['sorties-non-inscrit']->getData();
            $sortiesInscrit = $form['sorties-inscrit']->getData();
            $sortiesPassees = $form['sorties-passees']->getData();

            $entityManager -> getRepository(Sortie::class) -> findBy()


        }

    }
}