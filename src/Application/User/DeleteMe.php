<?php

namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;

final class DeleteMe
{
  public function __construct(
    private UserRepository $repository
  ) {}

  public function execute(User $user): void
  {
    $this->repository->delete($user);
    return;
  }
}
