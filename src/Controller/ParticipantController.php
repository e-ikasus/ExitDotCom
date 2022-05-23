<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantCsvType;
use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Services\CreateParticipantFromCSV;
use App\Services\ImportFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/admin", name="participant_list", methods={"GET", "POST"})
     */
    public function list(Request $request, ParticipantRepository $participantRepository, CampusRepository $campusRepository, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher): Response
    {

        $form = $this->createForm(ParticipantCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('moncsv')->getData();

            $importFile = new ImportFile($file, $this->getParameter('csv_files_directory'), $slugger, "csv");
            $importFile->storeFile();

            $createParticipantFromCSV = new CreateParticipantFromCSV($this->getParameter('csv_files_directory'), $importFile->getNewFileName(), $participantRepository, $campusRepository);
            $createParticipantFromCSV->importUser();

            return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/list.html.twig', [
            'participants' => $participantRepository->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/new", name="participant_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParticipantRepository $participantRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $participant = new Participant();



        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setPassword($passwordHasher->hashPassword( $participant, $form->get('password')->getData()));

            $participant->setRoles($form->get('administrateur')->getData() ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $participant->setActif(true);

            $participantRepository->add($participant, true);

            return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/profil/{pseudo}", name="participant_profil", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/edit/{pseudo}", name="participant_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Participant $participant, ParticipantRepository $participantRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoFile = $form->get('photo')->getData();

            $importFile = new ImportFile($photoFile, $this->getParameter('profiles_pictures_directory'), $slugger);
            $importFile->storeFile();

            $participant->setPhoto($importFile->getNewFileName());


            $participantRepository->add($participant, true);

            return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);

        }
        return $this->renderForm('participant/edit.html.twig', ['participant' => $participant,
            'form' => $form,]);
    }


    /**
     * @Route("/admin/{pseudo}", name="participant_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
    }

}
