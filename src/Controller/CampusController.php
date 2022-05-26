<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\SearchByNameType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/campus")
 */
class CampusController extends AbstractController
{
		/**
		 * Affiche la liste des campus. Si la fonction récupère un formulaire valide, alors un nouveau campus est ajouté à
		 * la base de données.
		 *
		 * @Route("/list", name="campus_list", methods={"GET", "POST"})
		 */

		public function list(CampusRepository $campusRepository, Request $request): Response
		{
				$campus = new Campus();

				// Crée le formulaire de recherche.
				$searchForm = $this->createForm(SearchByNameType::class, null);
				$searchForm->handleRequest($request);

				// Crée le formulaire et récupère un éventuel campus à ajouter.
				$form = $this->createForm(CampusType::class, $campus);
				$form->handleRequest($request);

				// Si une ville valide doit être ajoutée à la base.
				if ($form->isSubmitted() && $form->isValid())
				{
						try
						{
								$campusRepository->add($campus, true);

								$this->addFlash('success', 'Le campus ' . $campus->getNom() . ' a été ajouté avec succès.');
						}
						catch (\Exception $exception)
						{
								$this->addFlash('warning', 'Le campus ' . $campus->getNom() . ' n\'a pu être ajouté!');
						}
				}

				// Dresse la liste des campus en fonction de critères ou pas.
				if ($searchForm->isSubmitted() && $searchForm->isValid() && ($pattern = $searchForm->get('pattern')->getData()))
						$listCampus = $campusRepository->findByCriteria($pattern);
				else
						$listCampus = $campusRepository->findBy(array(), array('nom' => 'ASC'));

				return $this->render('campus/list.html.twig', [
						'campus' => $listCampus,
						'form' => $form->createView(),
						'searchForm' => $searchForm->createView()
				]);
		}

		/**
		 * Affiche les informations d'un campus.
		 *
		 * @Route("/{id}", name="campus_show", methods={"GET"})
		 */

		public function show(Campus $campus): Response
		{
				return $this->render('campus/show.html.twig', ['campus' => $campus]);
		}

		/**
		 * Modifie les données d'un campus.
		 *
		 * @Route("/edit/{id}", name="campus_edit", methods={"GET", "POST"})
		 */

		public function edit(Request $request, Campus $campus, CampusRepository $campusRepository): Response
		{
				$form = $this->createForm(CampusType::class, $campus);
				$form->handleRequest($request);

				if ($form->isSubmitted() && $form->isValid())
				{
						try
						{
								$campusRepository->add($campus, true);

								$this->addFlash('success', 'Les données du campus ' . $campus->getNom() . ' ont été modifiées avec succès.');
						}
						catch (\Exception $exception)
						{
								$this->addFlash('warning', 'Le campus ' . $campus->getNom() . ' n\'a pu être modifié!');
						}

						return $this->redirectToRoute('campus_list', [], Response::HTTP_SEE_OTHER);
				}

				return $this->renderForm('campus/edit.html.twig', ['campus' => $campus, 'form' => $form]);
		}

		/**
		 * Supprime un campus.
		 *
		 * @Route("/{id}", name="campus_delete", methods={"POST"})
		 */

		public function delete(Request $request, Campus $campus, CampusRepository $campusRepository): Response
		{
				if ($this->isCsrfTokenValid('delete' . $campus->getId(), $request->request->get('_token')))
				{
						try
						{
								$campusRepository->remove($campus, true);

								$this->addFlash('success', 'La supression du campus ' . $campus->getNom() . ' a été effectuée avec succès.');
						}
						catch (\Exception $exception)
						{
								$this->addFlash('warning', 'Vous ne pouvez pas supprimer le campus ' . $campus->getNom() . ' car il est référencé dans la base de données!!!');
						}
				}

				return $this->redirectToRoute('campus_list', [], Response::HTTP_SEE_OTHER);
		}
}
