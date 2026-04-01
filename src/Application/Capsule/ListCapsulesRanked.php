<?php

namespace App\Application\Capsule;

use App\Repository\CapsuleRepository;
use App\Entity\Capsule;

final class ListCapsulesRanked
{
  public function __construct(
    private CapsuleRepository $capsuleRepository
  ) {}

  /**
   * @return Capsule[]
   */
  public function execute(): array
  {
    return $this->capsuleRepository->findRanked();
  }
}
