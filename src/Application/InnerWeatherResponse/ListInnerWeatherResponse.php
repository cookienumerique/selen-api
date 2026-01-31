<?php

namespace App\Application\InnerWeatherResponse;

use App\Repository\InnerWeatherResponseRepository;

final class ListInnerWeatherResponse
{
  public function __construct(
    private InnerWeatherResponseRepository $repository
  ) {}

  /**
   * @return InnerWeatherResponse[]
   */
  public function execute(array $filters = []): array
  {
    return $this->repository->findByFilters($filters);
  }
}
