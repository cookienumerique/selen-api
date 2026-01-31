<?php

namespace App\Repository;

use App\Entity\InnerWeatherResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\InnerWeather;

/**
 * @extends ServiceEntityRepository<InnerWeatherResponse>
 */
class InnerWeatherResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InnerWeatherResponse::class);
    }

    public function hasResponseForDay(User $user): bool
    {
        $day = new \DateTimeImmutable('today');
        $result = $this->createQueryBuilder('iwr')
            ->select('1')
            ->andWhere('iwr.author = :user')
            ->andWhere('iwr.day = :day')
            ->setParameter('user', $user)
            ->setParameter('day', $day)
            ->getQuery()
            ->getOneOrNullResult();

        return $result !== null;
    }

    public function findByFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('iwr')
            ->andWhere('iwr.author = :author')
            ->setParameter('author', $filters['author']);

        if (!empty($filters['day'])) {
            $day = $filters['day'];
            $qb
                ->andWhere('iwr.day = :day')
                ->setParameter('day', $day);
        }
        return $qb->getQuery()->getResult();
    }
}
