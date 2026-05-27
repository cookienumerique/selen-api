<?php

namespace App\Application\InnerWeather;

use App\Entity\InnerWeather;
use App\Repository\InnerWeatherRepository;

final class ListInnerWeather
{
  public function __construct(
    private InnerWeatherRepository $repository
  ) {}

  /**
   * @return InnerWeather[]
   */
  public function execute(): array
  {
    return $this->repository->findAll();
  }
}
