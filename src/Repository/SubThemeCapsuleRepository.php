<?php

namespace App\Repository;

use App\Entity\SubThemeCapsule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\QueryBuilder;

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
        $qb = $this->createQueryBuilder('stc');

        $this->applyCodeCriteria($qb, $criteria);

        return $qb->getQuery()->getResult();
    }

    public function findWithProgressByCriteria(array $params, UserInterface $user): array
    {
        $qb = $this->createQueryBuilder('stc')
            ->select('stc')
            ->addSelect('COUNT(DISTINCT c.id) as totalCapsules')
            ->addSelect('COUNT(DISTINCT cr.id) as answeredCapsules')
            ->leftJoin('stc.capsules', 'c')
            ->leftJoin('c.capsuleResponses', 'cr', 'WITH', 'cr.author = :user')
            ->setParameter('user', $user)
            ->groupBy('stc.id');

        if (isset($params['code'])) {
            $this->applyCodeCriteria($qb, $params);
        }
        return $qb->getQuery()->getResult();
    }

    private function applyCodeCriteria(QueryBuilder $qb, array $params): void
    {
        if (!isset($params['code'])) return;

        $qb->andWhere('stc.code IN (:codes)')
            ->setParameter('codes', (array) $params['code']);
    }
}
