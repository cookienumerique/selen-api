<?php

namespace App\Application\InnerWeather;

use App\Entity\InnerWeather;
use App\Repository\InnerWeatherRepository;
use App\Exception\NotFoundException;

final class GetInnerWeather
{
  public function __construct(
    private InnerWeatherRepository $repository
  ) {}

  /**
   * @return InnerWeather
   */
  public function execute(int $innerWeatherId): InnerWeather
  {
    $innerWeather = $this->repository->findOneBy(['id' => $innerWeatherId]);
    if (!$innerWeather) {
      throw new NotFoundException('Inner weather not found');
    }
    return $innerWeather;
  }
}
