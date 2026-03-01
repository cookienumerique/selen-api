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

        // Achat / renewal / recovery
        case 1: // RECOVERED
        case 2: // RENEWED
        case 4: // PURCHASED
        case 7: // RESTARTED
          $this->googleSubscriptionVerifier->execute(
            $productId,
            $purchaseToken
          );
          $this->logger->info('Subscription verified and updated');
          return;

        case 3: // CANCELED (auto-renew off)
          $subscription->setAutoRenew(false);
          break;

        case 5: // ON_HOLD (payment failed)
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_ON_HOLD);
          break;

        case 6: // IN_GRACE_PERIOD
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_IN_GRACE_PERIOD);
          break;

        case 10: // PAUSED
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_PAUSED);
          break;

        case 12: // REVOKED (refund / fraud)
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_REVOKED);
          $subscription->setAutoRenew(false);
          break;

        case 13: // EXPIRED
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_EXPIRED);
          $subscription->setAutoRenew(false);
          break;

        default:
          $this->logger->warning('Unhandled Google RTDN notification type', [
            'type' => $notificationType
          ]);
          return;
      }
      $this->subscriptionRepository->save($subscription);
    } catch (\Throwable $e) {
      $this->logger->critical('Google RTDN unexpected error', [
        'error' => $e->getMessage(),
      ]);
    }
  }
}
