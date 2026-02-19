<?php

namespace App\Application\CapsuleResponse;

use App\Repository\CapsuleResponseRepository;
use App\Exception\CapsuleResponseNotFoundException;
use App\Exception\UnauthorizedCapsuleAccessException;
use App\Entity\User;
use App\Entity\CapsuleResponse;
use App\Application\Subscription\UserHasValidSubscription;
use App\Exception\Subscription\PremiumFeatureRequiredException;

final class UpdateCapsuleResponse
{
  public function __construct(
    private CapsuleResponseRepository $repository,
    private UserHasValidSubscription $userHasValidSubscription,
  ) {}

  public function execute(
    int $id,
    string $response,
    User $user
  ): CapsuleResponse {

    $userHasValidSubscription = $this->userHasValidSubscription->execute($user);

    if (!$userHasValidSubscription) {
      throw new PremiumFeatureRequiredException();
    }

    $capsuleResponse = $this->repository->find($id);

    if (!$capsuleResponse) {
      throw new CapsuleResponseNotFoundException();
    }
    if ($capsuleResponse->getAuthor()->getId() !== $user->getId()) {
      throw new UnauthorizedCapsuleAccessException();
    }

    $capsuleResponse->setResponse($response);

    $this->repository->save($capsuleResponse);

    return $capsuleResponse;
  }
}
