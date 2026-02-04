<?php

namespace App\Application\SubThemeCapsule;

use App\Repository\SubThemeCapsuleRepository;

final class ListSubThemeCapsules
{
  public function __construct(
    private SubThemeCapsuleRepository $repository
  ) {}

  /**
   * @return SubThemeCapsule[]
   */
  public function execute(array $params): array
  {
    if (isset($params['code'])) {
      $params['code'] = explode(',', $params['code']);
    }
    return $this->repository->findByCriteria($params);
  }
}
