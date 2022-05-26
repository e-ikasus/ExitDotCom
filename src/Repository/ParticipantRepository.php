<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Participant>
 *
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
		public function __construct(ManagerRegistry $registry)
		{
				parent::__construct($registry, Participant::class);
		}

		public function add(Participant $entity, bool $flush = false): void
		{
				$this->getEntityManager()->persist($entity);

				if ($flush)
				{
						$this->getEntityManager()->flush();
				}
		}

		public function remove(Participant $entity, bool $flush = false): void
		{
				$this->getEntityManager()->remove($entity);

				if ($flush)
				{
						$this->getEntityManager()->flush();
				}
		}

		/**
		 * Used to upgrade (rehash) the user's password automatically over time.
		 */
		public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
		{
				if (!$user instanceof Participant)
				{
						throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
				}

				$user->setPassword($newHashedPassword);

				$this->add($user, true);
		}

		/**
		 * Récupère la liste des participants ordonnés par le nom du campus auquel chaque participant appartient.
		 *
		 * @param string $order Ordre de tri.
		 *
		 * @return float|int|mixed|string
		 */

		public function findAllOrderedByCampus(string $order)
		{
				$queryBuilder = $this->createQueryBuilder('p');
				$queryBuilder->Join('p.campus', 'c');
				$queryBuilder->addSelect('c');
				$queryBuilder->addOrderBy("c.nom", $order);

				return $queryBuilder->getQuery()->getResult();
		}

		public function loadUserByIdentifier(string $identifier): ?Participant
		{
				$entityManager = $this->getEntityManager();

				return $entityManager->createQuery(
						'SELECT u
                FROM App\Entity\Participant u
                WHERE u.pseudo = :query
                OR u.email = :query'
				)
						->setParameter('query', $identifier)
						->getOneOrNullResult();
		}

		public function __call($name, $arguments)
		{
				// TODO: Implement @method null loadUserByIdentifier(string $identifier)
		}

		public function loadUserByUsername(string $username)
		{
				// TODO: Implement loadUserByUsername() method.
		}
}
