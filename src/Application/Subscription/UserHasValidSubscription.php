<?php

namespace App\Application\Subscription;


use App\Entity\User;
use App\Application\Subscription\GetActiveSubscriptionByUser;

final class UserHasValidSubscription
{
  public function __construct(
    private GetActiveSubscriptionByUser $getActiveSubscriptionByUser
  ) {}

  public function execute(User $user): bool
  {
    return $this->getActiveSubscriptionByUser->execute($user) ? true : false;
  }
}
