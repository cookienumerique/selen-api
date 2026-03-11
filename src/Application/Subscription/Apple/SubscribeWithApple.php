<?php

namespace App\Application\Subscription\Apple;

use App\Entity\Subscription;
use App\Infrastructure\Apple\AppleTransactionInfo;
use App\Exception\InvalidSubscriptionException;
use App\Enum\Subscription\SubscriptionProvider;
use App\Repository\SubscriptionRepository;
use App\Entity\User;
use App\Enum\Subscription\SubscriptionProductId;
use App\Enum\Subscription\SubscriptionBasePlanId;

final class SubscribeWithApple
{
  public function __construct(
    private SubscriptionRepository $subscriptionRepository,
  ) {}

  public function execute(User $user, AppleTransactionInfo $appleTransactionInfo): Subscription
  {
    $originalTransactionId = $appleTransactionInfo->originalTransactionId;
    $expiresDate = $appleTransactionInfo->expiresDate;
    $basePlanId = SubscriptionBasePlanId::fromAppleProductId($appleTransactionInfo->productId);

    if (!$originalTransactionId || !$basePlanId || !$expiresDate) {
      throw new InvalidSubscriptionException("Missing required Apple transaction data : originalTransactionId, basePlanId, expiresDate");
    }

    $expiresAt = (new \DateTimeImmutable())->setTimestamp($expiresDate / 1000);
    $status = $appleTransactionInfo->getStatus();

    $subscription = $this->subscriptionRepository->findOneBy([
      'provider' => SubscriptionProvider::APPLE,
      'providerSubscriptionId' => $appleTransactionInfo->originalTransactionId,
    ]);

    if ($subscription && $subscription->getUser()->getId() !== $user->getId()) {
      throw new InvalidSubscriptionException(
        'Receipt already attached to another user'
      );
    }

    if (!$subscription) {
      $subscription = new Subscription();
      $subscription
        ->setUser($user)
        ->setProvider(SubscriptionProvider::APPLE)
        ->setProviderSubscriptionId($appleTransactionInfo->originalTransactionId);
    }

    $subscription
      ->setProductId(SubscriptionProductId::SELEN_PREMIUM)
      ->setBasePlanId($basePlanId)
      ->setStatus($status)
      ->setExpiresAt($expiresAt)
      ->setAutoRenew(true)
      ->setUpdatedAt(new \DateTimeImmutable());

    $this->subscriptionRepository->save($subscription);

    return $subscription;
  }
}
