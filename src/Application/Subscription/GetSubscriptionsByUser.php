<?php

namespace App\Application\Subscription;

use App\Repository\SubscriptionRepository;
use App\Entity\User;

class GetSubscriptionsByUser
{
  public function __construct(
    private SubscriptionRepository $subscriptionRepository,
  ) {}

  public function execute(User $user): array
  {
    return $this->subscriptionRepository->findBy([
      'user' => $user,
    ]);
  }
}
