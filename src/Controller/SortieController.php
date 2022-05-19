<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\RechercheSortiesType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\Research;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
	 * @Route("/list", name="sortie_list", methods={"POST", "GET"})
	 */
	public function searchInList(Security $security, SortieRepository $sortieRepository, Request $request): Response
	{
		$user = $security->getUser();

		$form = $this->createForm(RechercheSortiesType::class, $this->research);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$sorties = $sortieRepository->findByCreteria($user, $this->research);
		}
		else
		{
			$sorties = $sortieRepository->findAll();
		}
		return $this->render('sortie/list.html.twig', [
						'sorties' => $sorties,
						'form' => $form->createView()
				]
		);
	}

	/**
	 * @Route("/new", name="sortie_new", methods={"GET", "POST"})
	 */
	public function new(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, Security $security): Response
	{
		$sortie = new Sortie();

        //Nous allons chercher les différents états via l'EtatRepository.
        //Nous créons une méthode qui sera auto-interprétée par Symfony pour aller chercher le bon libellé selon la constante dans l'entity Etat.
        $etatCreee = $etatRepository->findByIdLibelle(Etat::CREEE);
        $sortie->setEtat($etatCreee[0]);

        //Nous récupérons l'instance du participant connecté.
        //Puis nous avons fixé la propriété campus de sortie selon le participant.
        $user = $security->getUser();
        $sortie->setCampus($user->getCampus());

        $sortie->setOrganisateur($user);

		$form = $this->createForm(SortieType::class, $sortie);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
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

		if ($form->isSubmitted() && $form->isValid())
		{
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
	public function delete( Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
	{
		if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token')))
		{
			$sortieRepository->remove($sortie, true);
		}

		return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
	}
}
