<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Enum\Subscription\SubscriptionStatus;


/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function save(Subscription $subscription): void
    {
        $this->getEntityManager()->persist($subscription);
        $this->getEntityManager()->flush();
    }

    public function findActiveForUser(User $user): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.status IN (:validStatuses)')
            ->andWhere('s.expiresAt > :now')
            ->setParameter('user', $user)
            ->setParameter('validStatuses', [
                SubscriptionStatus::SUBSCRIPTION_STATE_ACTIVE,
                SubscriptionStatus::SUBSCRIPTION_STATE_IN_GRACE_PERIOD,
            ])
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('s.expiresAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
