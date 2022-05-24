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

/**
 * @Route("/groupe/prive")
 */
class GroupePriveController extends AbstractController
{
    /**
     * @Route("/list", name="groupe_prive_list", methods={"GET"})
     */
    public function index(GroupePriveRepository $groupePriveRepository): Response
    {
        return $this->render('groupe_prive/list.html.twig', [
            'groupe_prives' => $groupePriveRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="groupe_prive_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GroupePriveRepository $groupePriveRepository, ParticipantRepository $participantRepository): Response
    {
        $groupePrive = new GroupePrive();
        $participants = $participantRepository->findAll();
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function edit(Request $request, GroupePrive $groupePrive, GroupePriveRepository $groupePriveRepository): Response
    {
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupePriveRepository->add($groupePrive, true);

            return $this->redirectToRoute('groupe_prive_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groupe_prive/edit.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="groupe_prive_delete", methods={"POST"})
     */
    public function delete(Request $request, GroupePrive $groupePrive, GroupePriveRepository $groupePriveRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupePrive->getId(), $request->request->get('_token'))) {
            $groupePriveRepository->remove($groupePrive, true);
        }

        return $this->redirectToRoute('groupe_prive_list', [], Response::HTTP_SEE_OTHER);
    }
}