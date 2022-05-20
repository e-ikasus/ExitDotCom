<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campus>
 *
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CampusRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Campus::class);
	}

	/**
	 * Récupère la liste des campus depuis la base de données. Le nom de chaque campus doit contenir le motif fourni en
	 * paramètre. La liste est triée par ordre croissant.
	 *
	 * @param string $pattern Ce que doit contenir le nom des campus à récupérer
	 *
	 * @return float|int|mixed|string
	 */

	public function findByCriteria(string $pattern)
	{
		$queryBuilder = $this->createQueryBuilder('c');
		$queryBuilder->andWhere('c.nom LIKE :name');
		$queryBuilder->addOrderBy('c.nom', 'ASC');
		$queryBuilder->setParameter('name', '%' . $pattern . '%');

		$query = $queryBuilder->getQuery();

		return $query->getResult();
	}

	/**
	 * Ajoute un nouveau campuis à la base de données.
	 *
	 * @param Campus $campus Campus à ajouter à la BD.
	 * @param bool   $flush  Mettre à jour ou pas la BD.
	 *
	 * @return void
	 */

	public function add(Campus $campus, bool $flush = false): void
	{
		$this->getEntityManager()->persist($campus);

		if ($flush) $this->getEntityManager()->flush();
	}

	/**
	 * Supprime un campus de la base de données.
	 *
	 * @param Campus $campus Campus à ajouter à la BD.
	 * @param bool   $flush  Mettre à jour ou pas la BD.
	 *
	 * @return void
	 */

	public function remove(Campus $campus, bool $flush = false): void
	{
		$this->getEntityManager()->remove($campus);

		if ($flush) $this->getEntityManager()->flush();
	}
}
