<?php

namespace App\Application\InnerWeatherResponse;

use App\Entity\InnerWeatherResponse;
use App\Repository\InnerWeatherResponseRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final class GetInnerWeatherResponseOfTheDay
{
  public function __construct(
    private InnerWeatherResponseRepository $innerWeatherResponseRepository,
  ) {}

  /**
   * @return InnerWeatherResponse
   */
  public function execute(UserInterface $user): ?InnerWeatherResponse
  {
    $start = new \DateTimeImmutable('today');
    $end = $start->modify('+1 day');

    return $this->innerWeatherResponseRepository
      ->createQueryBuilder('i')
      ->andWhere('i.author = :user')
      ->andWhere('i.createdAt >= :start')
      ->andWhere('i.createdAt < :end')
      ->setParameter('user', $user)
      ->setParameter('start', $start)
      ->setParameter('end', $end)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
