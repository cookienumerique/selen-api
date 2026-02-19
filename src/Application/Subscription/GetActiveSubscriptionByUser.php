<?php

namespace App\Application\Subscription;

use App\Repository\SubscriptionRepository;
use App\Entity\Subscription;
use App\Entity\User;

class GetActiveSubscriptionByUser
{
  public function __construct(
    private SubscriptionRepository $subscriptionRepository,
  ) {}

  public function execute(User $user): ?Subscription
  {
    return $this->subscriptionRepository->findActiveForUser($user);
  }
}
