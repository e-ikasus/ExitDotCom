<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Form\GroupePriveType;
use App\Repository\GroupePriveRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/groupe/prive")
 */
class GroupePriveController extends AbstractController
{
    /**
     * @Route("/list", name="groupe_prive_list", methods={"GET"})
     */
    public function list(Security $security): Response
    {
        $user = $security->getUser();

        $listeGroupesOrganises = $user->getGroupesOrganises();

        return $this->render('groupe_prive/list.html.twig', [
            'groupe_prives' => $listeGroupesOrganises,
        ]);
    }

    /**
     * @Route("/new", name="groupe_prive_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GroupePriveRepository $groupePriveRepository, ParticipantRepository $participantRepository, Security $security): Response
    {
        $groupePrive = new GroupePrive();

        //Récupération des paramètres passés dans l'URL.
        $col = $request->get('col');
        $order = $request->get('order');

        //Si le tableau est null, càd aucun filtre n'a été appliqué, alors on le trie sur les pseudos, par ordre alphabétique ascendant.
        if($col == null) $col = 'pseudo';
        if($order == null) $order = 'ASC';

        $participants = $participantRepository->findBy([], [$col => $order]);

        $user = $security->getUser();

        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Le nom de la checkbox correspond à l'id du participant.
            //Pour chaque checkbox cochée, si son nom = id d'un participant, alors mettre le participant dans la liste
            foreach ($request->get('participants') as $idParticipant => $value) {
                //                                        = association clé->valeur
                $groupePrive->addParticipant($participantRepository->find($idParticipant));
            }

            //set le createur du groupePrive par le user
            $groupePrive->setCreateur($user);

            $groupePriveRepository->add($groupePrive, true);

            return $this->redirectToRoute('groupe_prive_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groupe_prive/new.html.twig', [
            'groupe_prive' => $groupePrive,
            'participants' => $participants,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="groupe_prive_show", methods={"GET"})
     */
    public function show(GroupePrive $groupePrive): Response
    {
        return $this->render('groupe_prive/show.html.twig', [
            'groupe_prive' => $groupePrive,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="groupe_prive_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, GroupePrive $groupePrive, GroupePriveRepository $groupePriveRepository, ParticipantRepository $participantRepository): Response
    {
        //montrer ceux déjà présents & retirer le user dans la liste
        //set les participants
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        //Récupération des paramètres passés dans l'URL.
        $col = $request->get('col');
        $order = $request->get('order');

        //Si le tableau est null, càd aucun filtre n'a été appliqué, alors on le trie sur les pseudos, par ordre alphabétique ascendant.
        if($col == null) $col = 'pseudo';
        if($order == null) $order = 'ASC';

        if ($form->isSubmitted() && $form->isValid()) {

            $participants = $groupePrive->getParticipant();

            //L'ancien groupe est vidé de ses participants.
            foreach ($participants as $participant) {
                $groupePrive->removeParticipant($participant);
            }

            //Afin de le remplir des nouveaux.
            foreach ($request->get('participants') as $idParticipant => $value) {
                $groupePrive->addParticipant($participantRepository->find($idParticipant));
            }

            $groupePriveRepository->add($groupePrive, true);

            return $this->redirectToRoute('groupe_prive_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groupe_prive/edit.html.twig', [
            'groupe_prive' => $groupePrive,
            'participants' => $participantRepository->findBy([], [$col => $order]),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="groupe_prive_delete", methods={"POST"})
     */
    public function delete(Request $request, GroupePrive $groupePrive, GroupePriveRepository $groupePriveRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $groupePrive->getId(), $request->request->get('_token'))) {
            $groupePriveRepository->remove($groupePrive, true);
        }

        return $this->redirectToRoute('groupe_prive_list', [], Response::HTTP_SEE_OTHER);
    }
}
