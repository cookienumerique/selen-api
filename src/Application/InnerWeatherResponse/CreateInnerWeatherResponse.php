<?php

namespace App\Application\InnerWeatherResponse;

use App\Entity\InnerWeatherResponse;
use App\Repository\InnerWeatherResponseRepository;
use App\Entity\InnerWeather;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\InnerWeatherAlreadyRespondedException;

final class CreateInnerWeatherResponse
{
  public function __construct(
    private EntityManagerInterface $em,
    private InnerWeatherResponseRepository $repository
  ) {}

  /**
   * @return InnerWeatherResponse
   */
  public function execute(InnerWeather $innerWeather, User $user): InnerWeatherResponse
  {
    if ($this->repository->hasResponseForDay($user, new \DateTimeImmutable('today'))) {
      throw new InnerWeatherAlreadyRespondedException();
    }

    $innerWeatherResponse = (new InnerWeatherResponse())
      ->setAuthor($user)
      ->setInnerWeather($innerWeather);

    $this->em->persist($innerWeatherResponse);
    $this->em->flush();

    return $innerWeatherResponse;
  }
}
