<?php

namespace App\Repository;

use App\Entity\CapsuleResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Capsule;

/**
 * @extends ServiceEntityRepository<CapsuleResponse>
 */
class CapsuleResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapsuleResponse::class);
    }

    public function isCapsuleAlreadyAnswered(User $user, Capsule $capsule): bool
    {
        return (bool) $this->createQueryBuilder('cr')
            ->select('1')
            ->andWhere('cr.author = :user')
            ->andWhere('cr.capsule = :capsule')
            ->setParameter('user', $user)
            ->setParameter('capsule', $capsule)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
