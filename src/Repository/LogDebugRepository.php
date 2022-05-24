<?php

namespace App\Repository;

use App\Entity\LogDebug;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogDebug>
 *
 * @method LogDebug|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogDebug|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogDebug[]    findAll()
 * @method LogDebug[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogDebugRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogDebug::class);
    }

    public function add(LogDebug $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) $this->getEntityManager()->flush();
    }

    public function remove(LogDebug $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) $this->getEntityManager()->flush();
    }
}
