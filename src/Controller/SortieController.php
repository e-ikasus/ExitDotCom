<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheSortiesType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Services\Research;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    public function __construct()
    {
        $this->research = new Research();
    }

     /**
     * @Route("/list", name="sortie_list", methods={"POST"})
     */
    public function searchInList(SortieRepository $sortieRepository, Request $request): Response
    {

        $form = $this->createForm(RechercheSortiesType::class, $this->research);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortieRepository->findByCreteria($this->research);
        } else {
            $sorties = $sortieRepository->findByAll();
        }
        return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/list", name="sortie_list", methods={"GET"})
     */
    public function list(SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(RechercheSortiesType::class, $this->research);
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sortieRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
    }
}
