<?php

namespace App\Application\Stats;

use App\Repository\CapsuleResponseRepository;

final class CountOpenedCapsulesResponse
{
  public function __construct(
    private CapsuleResponseRepository $repository
  ) {}

  public function execute(): int
  {
    $capsuleResponse = $this->repository->countOpenedCapsules();

    return $capsuleResponse;
  }
}
