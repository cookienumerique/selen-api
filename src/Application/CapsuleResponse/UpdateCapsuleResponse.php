<?php

namespace App\Application\CapsuleResponse;

use App\Repository\CapsuleResponseRepository;
use App\Exception\CapsuleResponseNotFoundException;
use App\Exception\UnauthorizedCapsuleAccessException;
use App\Entity\User;
use App\Entity\CapsuleResponse;
use App\Application\Subscription\UserHasValidSubscription;
use App\Exception\Subscription\PremiumFeatureRequiredException;
use App\Application\Ai\GenerateAIResponse;

final class UpdateCapsuleResponse
{
  public function __construct(
    private CapsuleResponseRepository $repository,
    private UserHasValidSubscription $userHasValidSubscription,
    private GenerateAIResponse $generateAIResponse
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

    $aiResponse = $this->generateAIResponse->execute($capsuleResponse->getCapsule()->getContent(), $response, $user);

    $capsuleResponse->setResponse($response)->setAiResponse($aiResponse);

    $this->repository->save($capsuleResponse);

    return $capsuleResponse;
  }
}
