<?php

namespace App\Repository;

use App\Entity\Capsule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Capsule>
 */
class CapsuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Capsule::class);
    }

    /**
     * @return Capsule[]
     */
    public function findByCriteria($criteria = []): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        if (!empty($criteria['subThemeCapsuleId'])) {
            $qb
                ->andWhere('c.subThemeCapsule = :subThemeId')
                ->setParameter('subThemeId', (int) $criteria['subThemeCapsuleId']);
        }

        return $qb->getQuery()->getResult();
    }
}
