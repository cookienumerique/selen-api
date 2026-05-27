<?php

namespace App\Application\CapsuleResponse;

use App\Repository\CapsuleResponseRepository;
use App\Entity\CapsuleResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Exception\NotFoundException;

final class GetCapsuleResponse
{
  public function __construct(
    private CapsuleResponseRepository $repository
  ) {}

  public function execute(int $id, UserInterface $author): CapsuleResponse|null
  {
    $capsuleResponse = $this->repository->findOneBy([
      'id' => $id,
      'author' => $author,
    ]);

    if (!$capsuleResponse) {
      throw new NotFoundException("Capsule response with id $id does not exist");
    }
    return $capsuleResponse;
  }
}
