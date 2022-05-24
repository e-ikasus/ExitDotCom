<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Services\Research;
use Container7GJzLhE\getDoctrine_QueryDqlCommandService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Sortie::class);
	}

	/**
	 * @return void
	 * @throws \Doctrine\DBAL\Exception
	 */

	public function refreshList()
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SET @state = 8; CALL check_sorties(0, FALSE, FALSE, TRUE, @state);';
		$stmt = $conn->prepare($sql);

		$stmt->executeQuery();
	}

	/**
	 * Ajoute une sortie à la base de données.
	 *
	 * @param Sortie $entity Sortie à ajouter
	 * @param bool   $flush  Mets à jour la base de données.
	 *
	 * @return void
	 */

	public function add(Sortie $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush)
		{
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Supprime une sortie de la base de données
	 *
	 * @param Sortie $entity Sortie à retirer/supprimer.
	 * @param bool   $flush  Mets à jour la base de données.
	 *
	 * @return void
	 */

	public function remove(Sortie $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush)
		{
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Requête pour le formulaire de recherche filtrée parmi la liste des sorties existantes.
	 *
	 * @param Participant $user     Utilisateur actuellement connecté
	 * @param Research    $research critère de recherche.
	 *
	 * @return Paginator Liste des sorties trouvées.
	 */

	public function findByCreteria(Participant $user, Research $research)
	{
		$queryBuilder = $this->createQueryBuilder('s');
		$queryBuilder->Join('s.campus', 'c');
		$queryBuilder->addSelect('c');
		$queryBuilder->addOrderBy("s.dateHeureDebut", "ASC");

		$queryBuilder->andWhere('s.campus = :campus');
		$queryBuilder->setParameter('campus', $research->getCampus());

        $queryBuilder->Join('s.etat', 'e');
        $queryBuilder->addSelect('e');

        //Filtre des sorties : les archivées ne sont plus consultables.
        $queryBuilder->andWhere('e.idLibelle != :idArchive');
        $queryBuilder->setParameter('idArchive', Etat::ARCHIVEE);

		// Si le nom d'une sortie doit contenir un terme en particulier.
		if ($research->getSearchOutingName())
		{
			$queryBuilder->andWhere('s.nom LIKE :name');
			$queryBuilder->setParameter('name', '%' . $research->getSearchOutingName() . '%');
		}

		// Si une date de début à été renseignée dans le formulaire.
		if ($research->getDateOutingStart())
		{
			$queryBuilder->andWhere('s.dateHeureDebut >= :dateOutingStart');
			$queryBuilder->setParameter('dateOutingStart', $research->getDateOutingStart());
		}

		// Si une date de fin à été renseignée dans le formulaire.
		if ($research->getDateOutingEnd())
		{
			$queryBuilder->andWhere('s.dateHeureDebut <= :dateOutingEnd');
			$queryBuilder->setParameter('dateOutingEnd', $research->getDateOutingEnd());
		}

		// S'il faut filtrer les sorties dont l'utilisateur en est l'organisateur.
		if ($research->getSortiesOrganisateur())
		{
			$queryBuilder->andWhere('s.organisateur = :organisateur');
			$queryBuilder->setParameter('organisateur', $user);
		}

		// S'il faut filtrer les sorties passées.
		if ($research->getSortiesPassees())
		{
			$queryBuilder->andWhere('e.idLibelle = :etat');
			$queryBuilder->setParameter('etat', Etat::TERMINEE);
		}

		// S'il faut filtrer les sorties auxquelles l'utilisateur est inscrit.
		if (($research->getSortiesInscrit()) && (!$research->getSortiesNonInscrit()))
		{
			$queryBuilder->Join('s.participants', 'parts');
			$queryBuilder->addSelect('parts');
			$queryBuilder->andWhere('parts = :part');
			$queryBuilder->setParameter('part', $user);
		}

		// S'il faut filtrer les sorties auxquelles l'utilisateur n'est pas inscrit.
		if (($research->getSortiesNonInscrit()) && (!$research->getSortiesInscrit()))
		{
			$queryBuilder->Join('s.participants', 'parts');
			$queryBuilder->addSelect('parts');
			$queryBuilder->andWhere('parts != :part');
			$queryBuilder->setParameter('part', $user);
			$queryBuilder->groupBy("s.id");
		}

		$query = $queryBuilder->getQuery();

		return new Paginator($query);
	}

    /**
     * Méthode afin d'occulter les sorties archivées en arrivant sur la liste des sorties.
     * @return float|int|mixed|string
     */
    public function findAllButArchived()
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->addOrderBy("s.dateHeureDebut", "ASC");

        $queryBuilder->Join('s.etat', 'e');
        $queryBuilder->addSelect('e');

        //Filtre des sorties : les archivées ne sont plus consultables.
        $queryBuilder->andWhere('e.idLibelle != :idArchive');
        $queryBuilder->setParameter('idArchive', Etat::ARCHIVEE);

        return $queryBuilder->getQuery()->getResult();
    }
}
