<?php

namespace App\Application\Capsule;

use App\Repository\CapsuleRepository;

final class ListCapsulesRanked
{
  public function __construct(
    private CapsuleRepository $capsuleRepository
  ) {}

  public function execute(array $criteria = []): array
  {
    $this->validateCriteria($criteria);

    return $this->capsuleRepository->findRanked($criteria);
  }

  private function validateCriteria(array $criteria): void
  {
    if (isset($criteria['subThemeCapsuleId']) && $criteria['subThemeCapsuleId'] < 1) {
      throw new \InvalidArgumentException('Invalid subThemeId');
    }
  }
}
