<?php

namespace App\Application\Subscription;

use App\Entity\User;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Enum\Subscription\SubscriptionProvider;
use App\Enum\Subscription\SubscriptionStatusAndroid;

final class GrantManualSubscription
{
  public function __construct(
    private SubscriptionRepository $subscriptionRepository,
  ) {}

  public function execute(User $user, int $months = 12): Subscription
  {
    $now = new \DateTimeImmutable();
    $expiresAt = $now->modify("+{$months} months");

    // Vérifie si déjà une active MANUAL
    $existing = $this->subscriptionRepository->findOneBy([
      'user' => $user,
      'provider' => SubscriptionProvider::MANUAL,
    ]);

    if ($existing) {
      $existing
        ->setExpiresAt($expiresAt)
        ->setStatus(SubscriptionStatusAndroid::SUBSCRIPTION_STATE_ACTIVE)
        ->setUpdatedAt(new \DateTimeImmutable());

      $this->subscriptionRepository->save($existing);

      return $existing;
    }

    $subscription = new Subscription();
    $subscription
      ->setUser($user)
      ->setProvider(SubscriptionProvider::MANUAL)
      ->setStatus(SubscriptionStatusAndroid::SUBSCRIPTION_STATE_ACTIVE)
      ->setExpiresAt($expiresAt)
      ->setAutoRenew(false)
      ->setProductId(null)
      ->setBasePlanId(null)
      ->setPurchaseToken('manual_' . uniqid())
      ->setOriginalTransactionId('manual_' . uniqid())
      ->setCreatedAt($now)
      ->setUpdatedAt($now);

    $this->subscriptionRepository->save($subscription);

    return $subscription;
  }
}
