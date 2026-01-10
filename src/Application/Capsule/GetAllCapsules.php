<?php

namespace App\Application\Capsule;

use App\Repository\CapsuleRepository;

final class GetAllCapsules
{
  public function __construct(
    private CapsuleRepository $capsuleRepository
  ) {}

  public function execute(): array
  {
    return $this->capsuleRepository->findAllOrdered();
  }
}
