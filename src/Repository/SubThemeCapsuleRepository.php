<?php

namespace App\Repository;

use App\Entity\SubThemeCapsule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubThemeCapsule>
 */
class SubThemeCapsuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubThemeCapsule::class);
    }

    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('s');
        if (isset($criteria['code'])) {
            if (is_array($criteria['code'])) {
                $qb
                    ->andWhere('s.code IN (:codes)')
                    ->setParameter('codes', $criteria['code']);
            } else {
                $qb
                    ->andWhere('s.code = :code')
                    ->setParameter('code', $criteria['code']);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
