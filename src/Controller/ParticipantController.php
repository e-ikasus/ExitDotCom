<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantCsvType;
use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Services\ImportFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/admin", name="participant_list", methods={"GET"})
     */
    public function list(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/list.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/new", name="participant_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParticipantRepository $participantRepository): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
     * @Route("/{pseudo}", name="participant_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/admin/neww", name="participant_new_csv", methods={"GET", "POST"})
     */
    public
    function newCSV(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CampusRepository $campusRepository): Response
    {
        $participantCSV = new Participant();
        $form = $this->createForm(ParticipantCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


                $file = $form->get('moncsv')->getData();

                $importFile = new ImportFile($file, $this->getParameter('csv_files_directory'), $slugger);
                $importFile->storeFile();

            if (($handle = fopen("upload/csv_files/" . $importFile->getNewFileName(), "r")) !== FALSE) {

                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                    $num = count($data);

                    $participantCSV->setCampus($campusRepository->findOneBy(array('nom'=>$data[0])));
                    $participantCSV->setPseudo($data[0]);
                    $participantCSV->setRoles($data[0] ? ["ROLE_ADMIN"] : ["ROLE_USER"]);
                    $participantCSV->setPassword(password_hash($data[0], PASSWORD_BCRYPT));
                    $participantCSV->setPrenom($data[0]);
                    $participantCSV->setNom($data[0]);
                    $participantCSV->setTelephone($data[0]);
                    $participantCSV->setEmail($data[0]);
                    $participantCSV->setAdministrateur($data[0]);
                    $participantCSV->setActif($data[0]);
                    $participantCSV->setPhoto('default.png');

                    $entityManager->persist($participantCSV);
                }
                fclose($handle);
                $entityManager->flush();
            }

            return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/newCSV.html.twig', [
            'form' => $form,
        ]);
    }

}
