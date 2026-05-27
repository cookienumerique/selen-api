<?php

namespace App\Application\CapsuleResponse;

use App\Repository\CapsuleResponseRepository;
use App\Entity\CapsuleResponse;
use Symfony\Component\Security\Core\User\UserInterface;

final class ListCapsulesResponses
{
  public function __construct(
    private CapsuleResponseRepository $repository
  ) {}

  /**
   * @return CapsuleResponse[]
   */
  public function execute(UserInterface $author): array
  {
    return $this->repository->findBy(['author' => $author]);
  }
}
