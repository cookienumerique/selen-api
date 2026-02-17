<?php

namespace App\Application\Subscription;

use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerInterface;

class GooglePlayNotificationHandler
{
  public function __construct(
    private VerifyAndroidSubscription $verifyAndroidSubscription,
    private SubscriptionRepository $subscriptionRepository,
    private LoggerInterface $logger
  ) {}

  public function execute(array $payload): void
  {
    try {

      if (!isset($payload['message']['data'])) {
        $this->logger->warning('Google RTDN: missing message.data');
        return;
      }

      // 1️⃣ Decode base64
      $decoded = base64_decode($payload['message']['data']);
      $notification = json_decode($decoded, true);

      if (!$notification) {
        $this->logger->warning('Google RTDN: invalid JSON after decode');
        return;
      }

      if (!isset($notification['subscriptionNotification'])) {
        // Ce n'est pas une notif subscription (ex: testNotification)
        $this->logger->info('Google RTDN: not a subscription notification');
        return;
      }

      $subscriptionNotification = $notification['subscriptionNotification'];

      $purchaseToken = $subscriptionNotification['purchaseToken'] ?? null;
      $subscriptionId = $subscriptionNotification['subscriptionId'] ?? null;
      $notificationType = $subscriptionNotification['notificationType'] ?? null;

      if (!$purchaseToken || !$subscriptionId) {
        $this->logger->warning('Google RTDN: missing purchaseToken or subscriptionId');
        return;
      }

      $this->logger->info('Google RTDN received', [
        'purchaseToken' => $purchaseToken,
        'subscriptionId' => $subscriptionId,
        'notificationType' => $notificationType
      ]);

      $subscription = $this->subscriptionRepository->findOneBy(['purchaseToken' => $purchaseToken]);
      if (!$subscription) {
        $this->logger->warning('Google RTDN: subscription not found');
        return;
      }

      $this->verifyAndroidSubscription->execute(
        $subscription->getUser(),
        $subscriptionId,
        $purchaseToken
      );
    } catch (\Throwable $e) {

      $this->logger->critical('Google RTDN unexpected error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      // On NE throw PAS.
      // Si tu throw, Pub/Sub retry en boucle.
    }
  }
}
