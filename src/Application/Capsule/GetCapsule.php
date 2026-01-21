<?php

namespace App\Application\Capsule;

use App\Repository\CapsuleRepository;
use App\Entity\Capsule;
use App\Exception\NotFoundException;

final class GetCapsule
{
  public function __construct(
    private CapsuleRepository $repository
  ) {}


  public function execute(int $id): Capsule
  {
    $capsule = $this->repository->find($id);

    if (!$capsule) {
      throw new NotFoundException("Capsule with id $id does not exist");
    }

    return $capsule;
  }
}
