<?php

namespace App\Application\SubThemeCapsule;

use App\Repository\SubThemeCapsuleRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Application\SubThemeCapsule\Dto\SubThemeCapsuleWithProgressDto;

final class ListSubThemeCapsulesWithProgress
{
  public function __construct(
    private SubThemeCapsuleRepository $repository,
  ) {}

  public function execute(UserInterface $user): array
  {

    $results = $this->repository->findWithProgressByUser($user);

    return array_map(fn($row) => new SubThemeCapsuleWithProgressDto(
      $row[0],
      (int) $row['totalCapsules'],
      (int) $row['answeredCapsules']
    ), $results);
  }
}
