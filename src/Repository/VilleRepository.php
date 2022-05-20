<?php

namespace App\Repository;

use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ville>
 *
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Ville::class);
	}

	/**
	 * Récupère la liste des villes depuis la base de données. Le nom de chaque ville doit contenir le motif fourni en
	 * paramètre. La liste est triée par ordre croissant.
	 *
	 * @param string $pattern Ce que doit contenir le nom des villes à récupérer
	 *
	 * @return float|int|mixed|string
	 */

	public function findByCriteria(string $pattern)
	{
		$queryBuilder = $this->createQueryBuilder('v');
		$queryBuilder->andWhere('v.nom LIKE :name');
		$queryBuilder->addOrderBy('v.nom', 'ASC');
		$queryBuilder->setParameter('name', '%' . $pattern . '%');

		$query = $queryBuilder->getQuery();

		return $query->getResult();
	}

	/**
	 * Ajoute une ville à la base de données.
	 *
	 * @param Ville $ville Ville à ajouter à la BD.
	 * @param bool  $flush Mettre à jour ou pas la BD.
	 *
	 * @return void
	 */

	public function add(Ville $ville, bool $flush = false): void
	{
		$this->getEntityManager()->persist($ville);

		if ($flush) $this->getEntityManager()->flush();
	}

	/**
	 * Supprime une ville de la base de données.
	 *
	 * @param Ville $ville Ville à supprimer de la BD.
	 * @param bool  $flush Mettre à jour ou pas la BD.
	 *
	 * @return void
	 */

	public function remove(Ville $ville, bool $flush = false): void
	{
		$this->getEntityManager()->remove($ville);

		if ($flush) $this->getEntityManager()->flush();
	}
}
