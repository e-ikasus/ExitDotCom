<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\ReasonForCancellationType;
use App\Form\RechercheSortiesType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\Research;
use Doctrine\ORM\EntityManagerInterface;
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
		/**
		 * @Route("/list", name="sortie_list", methods={"POST", "GET"})
		 */
		public function searchInList(Security $security, SortieRepository $sortieRepository, Request $request): Response
		{
				$research = new Research();

				$user = $security->getUser();

				$form = $this->createForm(RechercheSortiesType::class, $research);
				$form->handleRequest($request);

				// Met à jour l'état des sorties.
				$sortieRepository->refreshList();

				//Récupération des paramètres passés dans l'URL.
				$col = $request->get('col');
				$order = $request->get('order');

				//Si le tableau est null, càd aucun filtre n'a été appliqué, alors on le trie sur les pseudos, par ordre alphabétique ascendant.
				if ($col == null) $col = 'nom';
				if ($order == null) $order = 'ASC';

				if ($form->isSubmitted() && $form->isValid())
				{
						$sorties = $sortieRepository->findByCreteria($user, $research, $col, $order);
				}
				else
				{
						$sorties = $sortieRepository->findAllButArchived($col, $order);
				}

				return $this->render('sortie/list.html.twig', ['sorties' => $sorties, 'form' => $form->createView()]);
		}

		/**
		 * @Route("/unsubscribe/{id}", name="sortie_unsubscribe", methods={"GET"})
		 */
		public function unsubscribe(Sortie $sortie, Security $security, EntityManagerInterface $entityManager): Response
		{
				$security->getUser()->removeSortiesInscrit($sortie);

				try
				{
						$entityManager->flush();
						$this->addFlash('success', 'Vous vous êtes bien désinscrit de la sortie ' . $sortie->getNom() . ' !');
				}
				catch (\Exception $exc)
				{
						$this->addFlash('danger', 'Opération impossible !');
				}

				return $this->redirectToRoute("sortie_list");
		}

		/**
		 * @Route("/subscribe/{id}", name="sortie_subscribe", methods={"GET"})
		 */
		public function subscribe(Sortie $sortie, Security $security, EntityManagerInterface $entityManager): Response
		{
				$security->getUser()->addSortiesInscrit($sortie);

				try
				{
						$entityManager->flush();
						$this->addFlash('success', 'Vous êtes inscrit à la sortie ' . $sortie->getNom() . ' !');
				}
				catch (\Exception $exc)
				{
						$this->addFlash('danger', 'Opération impossible !');
				}

				return $this->redirectToRoute("sortie_list");
		}

		/**
		 * @Route("/new", name="sortie_new", methods={"GET", "POST"})
		 * @Route("/{id}/edit", name="sortie_edit", methods={"GET", "POST"})
		 */
		public function new(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, Security $security): Response
		{
				if ($request->get('_route') == "sortie_new")
				{
						$sortie = new Sortie();

						$form = $this->createForm(SortieType::class, $sortie);
						$form->handleRequest($request);

						//Nous allons chercher les différents états via l'EtatRepository.
						//Nous créons une méthode qui sera auto-interprétée par Symfony pour aller chercher le bon libellé selon la constante dans l'entity Etat.

						if ($request->get('enregistrer'))
						{
								$etatCreee = $etatRepository->findOneBy(array('idLibelle' => Etat::ENCREATION));
						}
						else
						{
								$etatCreee = $etatRepository->findOneBy(array('idLibelle' => Etat::OUVERTE));
						}
						$sortie->setEtat($etatCreee);

						// Nous récupérons l'instance du participant connecté.
						// Puis nous avons fixé la propriété campus de sortie selon le participant.
						$user = $security->getUser();
						$sortie->setCampus($user->getCampus());

						$sortie->setOrganisateur($user);
				}
				else
				{
						$sortie = $sortieRepository->findOneBy(array('id' => $request->get('id')));
						$form = $this->createForm(SortieType::class, $sortie);
						$form->handleRequest($request);

						if ($request->get('enregistrer'))
								$etatCreee = $etatRepository->findOneBy(array('idLibelle' => Etat::ENCREATION));
						else
								$etatCreee = $etatRepository->findOneBy(array('idLibelle' => Etat::OUVERTE));

						$sortie->setEtat($etatCreee);
				}

				if ($form->isSubmitted() && $form->isValid())
				{
						try
						{
								$sortieRepository->add($sortie, true);

								$this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a bien été mise à jour.');
						}
						catch (\Exception $exception)
						{
								$this->addFlash('warning', 'La sortie ' . $sortie->getNom() . ' n\'a pu être mise à jour!');
						}

						return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
				}

				return $this->renderForm('sortie/new.html.twig', ['sortie' => $sortie, 'form' => $form,]);
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
		 * @Route("/{id}/cancel", name="sortie_cancellation", methods={"GET", "POST"})
		 */
		public function cancel(Request $request, Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
		{
				$form = $this->createForm(ReasonForCancellationType::class);
				$form->handleRequest($request);

				if ($form->isSubmitted() && $form->isValid())
				{
						$sortie->setInfosSortie($form->get('motifAnnulation')->getData());

						$etatCreee = $etatRepository->findOneBy(array('idLibelle' => Etat::ANNULEE));
						$sortie->setEtat($etatCreee);

						$this->addFlash('success', 'La sortie a été annulée avec succès !');

						$entityManager->persist($sortie);
						$entityManager->flush();

						return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
				}
				return $this->render('sortie/annulation_sortie.html.twig', ['sortie' => $sortie, 'cancellation' => $form->createView()]);
		}

        /**
         * Supprime une sortie en création.
         *
         * @Route("/{id}/delete", name="sortie_delete", methods={"GET", "POST"})
         */
        public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
        {
            if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
                try {
                    $sortieRepository->remove($sortie, true);

                    $this->addFlash('success', 'Suppression de la sortie effectuée avec succès.');
                } catch (\Exception $exception) {
                    $this->addFlash('warning', 'Vous ne pouvez pas supprimer la sortie.');
                }
            }
            return $this->redirectToRoute('sortie_list', [], Response::HTTP_SEE_OTHER);
        }

}
