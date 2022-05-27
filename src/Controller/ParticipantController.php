<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantCsvType;
use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Services\CreateParticipantFromCSV;
use App\Services\ImportFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/admin", name="participant_list", methods={"GET", "POST"})
     */
    public function list(Request $request, ParticipantRepository $participantRepository, CampusRepository $campusRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager, Security $security): Response
    {

        //Récupération des paramètres passés dans l'URL.
        $col = $request->get('col');
        $order = $request->get('order');

        //Si le tableau est null, càd aucun filtre n'a été appliqué, alors on le trie sur les pseudos, par ordre alphabétique ascendant.
        if ($col == null) $col = 'pseudo';
        if ($order == null) $order = 'ASC';
        //association du formulaire à la variable $form puis récupération des données du formulaire
        $form = $this->createForm(ParticipantCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('moncsv')->getData();

            $importFile = new ImportFile($file, $this->getParameter('csv_files_directory'), $slugger, "csv");
            $importFile->storeFile();

            $createParticipantFromCSV = new CreateParticipantFromCSV($this->getParameter('csv_files_directory'), $importFile->getNewFileName(), $campusRepository, $entityManager);

            try {

                $createParticipantFromCSV->importUser();
                $this->addFlash('success', "L'import des utilisateurs du fichier CSV est un succès.");

            } catch (\Exception $exception) {
                $this->addFlash('danger', "L'import des utilisateurs du fichier CSV a échoué, veillez a ce qu'aucun des nouveaux utilisateurs ne soit déjà présent dans la base et que les données des utilisateurs soient conformes au exigences du site.");
            }

            return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted() && !$form->isValid()){

            $this->addFlash('danger', 'Merci d\'importer un fichier au format csv, un template est disponible au téléchargement via un clic sur le lien "Télécharger Template".');
        }

        if ($request->get("resultat") != null) {

            $resultat = $request->get("resultat");
            foreach ($resultat as $id) {
                $participant = $participantRepository->findOneBy(array("id" => $id));
                $participant->setActif(!$participant->isActif());
            }
            $entityManager->flush();

            return $this->redirectToRoute('participant_list');
        }

        // Récupère la liste des participants triés en fonction du choix de l'utilisateur.
        $participants = ($col == 'campus') ? ($participantRepository->findAllOrderedByCampus($order)) : ($participantRepository->findBy([], [$col => $order]));

        return $this->renderForm('participant/list.html.twig', ['participants' => $participants, 'form' => $form]);
    }

    /**
     * @Route("/admin/new", name="participant_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParticipantRepository $participantRepository, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {
        $participant = new Participant();

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('photo')->getData() != null) {
                $photoFile = $form->get('photo')->getData();
                $importFile = new ImportFile($photoFile, $this->getParameter('profiles_pictures_directory'), $slugger);
                $importFile->storeFile();
                $participant->setPhoto($importFile->getNewFileName());
            }
            //on hydrate
            $participant->setPassword($passwordHasher->hashPassword($participant, $form->get('password')->getData()));
            $participant->setRoles($form->get('administrateur')->getData() ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $participant->setActif(true);

            try {
                $participantRepository->add($participant, true);

                $this->addFlash('success', 'Vous venez d\'ajouter ' . $participant->getPrenom() . ' ' . $participant->getNom() . ' comme participant.');
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'L\'ajout de ' . $participant->getPrenom() . ' ' . $participant->getNom() . ' n\'a pu se faire!');
            }
            //redirection vers la liste des participants
            return $this->redirectToRoute('participant_list');
        }

        return $this->renderForm('participant/new.html.twig', ['participant' => $participant, 'form' => $form,]);
    }

    /**
     * @Route("/profil/{pseudo}", name="participant_profil", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', ['participant' => $participant,]);
    }

    /**
     * @Route("/edit/{pseudo}", name="participant_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Participant $participant, ParticipantRepository $participantRepository, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher, Security $security): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);

        if (!$security->getUser()->isAdministrateur()) {
            $form->remove('administrateur');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('photo')->getData() != null) {
                $photoFile = $form->get('photo')->getData();
                $importFile = new ImportFile($photoFile, $this->getParameter('profiles_pictures_directory'), $slugger);
                $importFile->storeFile();
                $participant->setPhoto($importFile->getNewFileName());
            }

            $participant->setPassword($passwordHasher->hashPassword($participant, $form->get('password')->getData()));

            try {
                $participantRepository->add($participant, true);

                $this->addFlash('success', 'L\'identité de ' . $participant->getPrenom() . ' ' . $participant->getNom() . ' a été modifiée avec succès.');
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'L\'identité de ' . $participant->getPrenom() . ' ' . $participant->getNom() . ' n\'a pu être modifiée!');
            }

            return $this->redirectToRoute('sortie_list');
        }

        return $this->renderForm('participant/edit.html.twig', ['participant' => $participant, 'form' => $form,]);
    }

    /**
     * @Route("/admin/{pseudo}", name="participant_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->request->get('_token'))) {
            // Toutes les sortie du participant doivent être "détachées" de celui-ci.
            foreach ($participant->getSortiesOrganisees() as $sortiesOrganisee)
                $sortiesOrganisee->setOrganisateur(null);

            try {
                // Supprime maintenant le participant. Seront également "flushées" les sorties précédemment modifiées.
                $participantRepository->remove($participant, true);

                // Avertit l'admin que tout s'est bien déroulé.
                $this->addFlash('success', 'Vous venez de vous débarrasser de ' . $participant->getPrenom() . ' ' . $participant->getNom() . ' . Veillez à bien effacer toutes les preuves, et vérifiez qu\'il n\'y ait pas de témoin gênant...!');
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'Impossible de virer ' . $participant->getPrenom() . ' ' . $participant->getNom() . '!');
            }
        }

        return $this->redirectToRoute('participant_list', [], Response::HTTP_SEE_OTHER);
    }
}
