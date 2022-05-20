<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\SearchByNameType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/ville")
 */
class VilleController extends AbstractController
{
	/**
	 * Affiche la liste des villes. Si la fonction récupère un formulaire valide, alors une nouvelle ville est ajoutée à
	 * la base de données.
	 *
	 * @Route("/list", name="ville_list", methods={"GET", "POST"})
	 */

	public function list(Request $request, VilleRepository $villeRepository): Response
	{
		$ville = new Ville();

		// Crée le formulaire de recherche.
		$searchForm = $this->createForm(SearchByNameType::class, null);
		$searchForm->handleRequest($request);

		// Crée le formulaire et récupère une éventuelle ville à ajouter.
		$form = $this->createForm(VilleType::class, $ville);
		$form->handleRequest($request);

		// Si une ville valide doit être ajoutée à la base.
		if ($form->isSubmitted() && $form->isValid()) $villeRepository->add($ville, true);

		// Dresse la liste des villes en fonction de critères ou pas.
		if ($searchForm->isSubmitted() && $searchForm->isValid() && ($pattern = $searchForm->get('pattern')->getData()))
			$listVilles = $villeRepository->findByCriteria($pattern);
		else
			$listVilles = $villeRepository->findAll();

		return $this->render('ville/list.html.twig', [
				'villes' => $listVilles,
				'form' => $form->createView(),
				'searchForm' => $searchForm->createView()
		]);
	}

	/**
	 * Affiche les informations d'une ville.
	 *
	 * @Route("/{id}", name="ville_show", methods={"GET"})
	 */

	public function show(Ville $ville): Response
	{
		return $this->render('ville/show.html.twig', ['ville' => $ville,]);
	}

	/**
	 * Edite les données d'une ville.
	 *
	 * @Route("/{id}/edit", name="ville_edit", methods={"GET", "POST"})
	 */

	public function edit(Request $request, Ville $ville, VilleRepository $villeRepository): Response
	{
		$form = $this->createForm(VilleType::class, $ville);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$villeRepository->add($ville, true);

			return $this->redirectToRoute('ville_list', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('ville/edit.html.twig', ['ville' => $ville, 'form' => $form->createView(),]);
	}

	/**
	 * Supprime une ville.
	 *
	 * @Route("/{id}/delete", name="ville_delete", methods={"GET", "POST"})
	 */

	public function delete(Request $request, Ville $ville, VilleRepository $villeRepository): Response
	{
		if ($this->isCsrfTokenValid('delete' . $ville->getId(), $request->request->get('_token')))
			$villeRepository->remove($ville, true);

		return $this->redirectToRoute('ville_list', [], Response::HTTP_SEE_OTHER);
	}
}
