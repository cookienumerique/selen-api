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

    /**
     * @description Find the ranked capsules ids (ordered by skip rate and total characters length)
     * @return int[]
     */
    public function findRankedIds(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
SELECT 
c.id,
COUNT(cr.id) as total_responses,
SUM(CASE WHEN cr.response IS NULL OR TRIM(cr.response) = '' THEN 1 ELSE 0 END) as skips,
SUM(CASE WHEN cr.response IS NOT NULL AND TRIM(cr.response) != '' THEN 1 ELSE 0 END) as filled,
ROUND(100.0 * SUM(CASE WHEN cr.response IS NULL OR TRIM(cr.response) = '' THEN 1 ELSE 0 END) / NULLIF(COUNT(cr.id), 0), 1) as skip_rate,
ROUND(AVG(CASE WHEN cr.response IS NOT NULL AND TRIM(cr.response) != '' THEN LENGTH(cr.response) ELSE NULL END), 1) as total_chars_length
FROM capsule c
LEFT JOIN capsule_response cr ON cr.capsule_id = c.id
GROUP BY c.id
ORDER BY skip_rate ASC, total_chars_length DESC;
SQL;

        return array_column($conn->fetchAllAssociative($sql), 'id');
    }

    public function findRanked(): array
    {
        $rankedCapsulesIds = $this->findRankedIds();

        if (empty($rankedCapsulesIds)) return [];

        $capsules = $this->findBy(['id' => $rankedCapsulesIds]);

        $indexed = [];
        foreach ($capsules as $capsule) {
            $indexed[$capsule->getId()] = $capsule;
        }

        return array_values(array_filter(
            array_map(fn($id) => $indexed[$id] ?? null, $rankedCapsulesIds)
        ));
    }
}
