<?php

namespace App\Application\Subscription;

use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerInterface;
use App\Enum\Subscription\SubscriptionStatus;
use App\Application\Subscription\Google\GoogleSubscriptionVerifier;

class GooglePlayNotificationHandler
{
  public function __construct(
    private GoogleSubscriptionVerifier $googleSubscriptionVerifier,
    private SubscriptionRepository $subscriptionRepository,
    private LoggerInterface $logger
  ) {}

  public function execute(string $purchaseToken, string $productId, int $notificationType): void
  {
    try {
      $this->logger->info("Processing Google RTDN type: $notificationType");

      $subscription = $this->subscriptionRepository->findOneBy(['providerSubscriptionId' => $purchaseToken]);

      if (!$subscription) {
        $this->logger->warning('Google RTDN: subscription not found locally for token', ['token' => $purchaseToken]);
        return;
      }

      switch ($notificationType) {
        case 2: // SUBSCRIPTION_PURCHASED
        case 7: // SUBSCRIPTION_RENEWED
        case 1: // SUBSCRIPTION_RECOVERED 
          $this->googleSubscriptionVerifier->execute(
            $productId,
            $purchaseToken
          );
          $this->logger->info('Subscription updated/renewed');
          return;
        case 3: // SUBSCRIPTION_CANCELED
          $subscription->setAutoRenew(false);
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_CANCELED);
          break;

        case 5: // SUBSCRIPTION_EXPIRED
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_EXPIRED);
          $subscription->setAutoRenew(false);
          break;
        case 12: // SUBSCRIPTION_REVOKED
          // On coupe l'accès immédiatement
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_EXPIRED);
          $subscription->setAutoRenew(false);
          break;
      }
      $this->subscriptionRepository->save($subscription);
    } catch (\Throwable $e) {
      $this->logger->critical('Google RTDN unexpected error', [
        'error' => $e->getMessage(),
      ]);
    }
  }
}
