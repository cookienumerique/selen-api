<?php

namespace App\Application\Subscription;

use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerInterface;
use App\Enum\Subscription\SubscriptionStatus;
use App\Application\Subscription\Google\GoogleSubscriptionVerifier;
use App\Enum\Subscription\SubscriptionProvider;
use App\Application\Subscription\Google\SubscribeWithGoogle;

class GooglePlayNotificationHandler
{
  public function __construct(
    private GoogleSubscriptionVerifier $googleSubscriptionVerifier,
    private SubscribeWithGoogle $subscribeWithGoogle,
    private SubscriptionRepository $subscriptionRepository,
    private LoggerInterface $logger,
  ) {}

  /**
   * Handle Google Real-Time Developer Notification (RTDN)
   */
  public function execute(array $subscriptionNotification): void
  {
    try {
      //   "subscriptionNotification" => array:4 [
      //     "version" => "1.0"
      //     "notificationType" => 11
      //     "purchaseToken" => "hopeciihinoglghpjfebdini.AO-J1OyQQTO6WoZ1uxjHj79mo9BOlZVllnHSg2JLbHcfKG1FMm3thR4DnC3DPBj2W21_um7K7hH5xJGbY2KqC4v7PWSkGHWwyVe2Muu1EFhJ1g8gFakd8Ug"
      //     "subscriptionId" => "selen_premium"
      //   ]
      $purchaseToken = $subscriptionNotification['purchaseToken'];
      $productId = $subscriptionNotification['subscriptionId'];
      $notificationType = $subscriptionNotification['notificationType'];
      $this->logger->info("Processing Google RTDN type: $notificationType");
      $googlePurchase = $this->googleSubscriptionVerifier->execute($productId, $purchaseToken);

      /**
       * ------------------------------------------------------------------
       * TYPES QUI NÉCESSITENT UNE RESYNCHRONISATION COMPLÈTE AVEC GOOGLE
       * ------------------------------------------------------------------
       * On ne décide jamais du statut nous-mêmes.
       * On laisse Google API être la source de vérité.
       */
      if (in_array($notificationType, [
        1,  // RECOVERED
        2,  // RENEWED
        4,  // PURCHASED
        7,  // RESTARTED
        9,  // DEFERRED
        11, // PAUSE_SCHEDULE_CHANGED
        17, // ITEMS_CHANGED
        19, // PRICE_CHANGE_UPDATED
        22, // PRICE_STEP_UP_CONSENT_UPDATED
      ], true)) {

        $subscription = $this->subscribeWithGoogle->executeFromWebhook($googlePurchase, $purchaseToken);
        $this->logger->info('Subscription fully resynced with Google', [
          'status' => $subscription->getStatus()->value
        ]);
        return;
      }

      /**
       * ------------------------------------------------------------------
       * TYPES QUI MODIFIENT UNIQUEMENT L'ÉTAT LOCAL
       * ------------------------------------------------------------------
       */
      $subscription = $this->subscriptionRepository->findOneBy([
        'providerSubscriptionId' => $purchaseToken,
        'provider' => SubscriptionProvider::GOOGLE
      ]);

      if (!$subscription) {
        $this->logger->warning('Google RTDN: subscription not found locally', [
          'token' => $purchaseToken
        ]);
        return;
      }

      switch ($notificationType) {

        case 3: // SUBSCRIPTION_CANCELED
          // L'utilisateur a désactivé l'auto-renew.
          // L'accès reste valide jusqu'à expiresAt.
          $subscription->setAutoRenew(false);
          break;

        case 5: // SUBSCRIPTION_ON_HOLD
          // Paiement échoué, accès suspendu.
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_ON_HOLD);
          break;

        case 6: // SUBSCRIPTION_IN_GRACE_PERIOD
          // Période de grâce avant blocage.
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_IN_GRACE_PERIOD);
          break;

        case 10: // SUBSCRIPTION_PAUSED
          // Abonnement suspendu temporairement.
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_PAUSED);
          break;

        case 12: // SUBSCRIPTION_REVOKED
          // Remboursement / fraude → accès coupé immédiatement.
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_REVOKED);
          $subscription->setAutoRenew(false);
          break;

        case 13: // SUBSCRIPTION_EXPIRED
          // Abonnement arrivé à expiration.
          $subscription->setStatus(SubscriptionStatus::SUBSCRIPTION_STATE_EXPIRED);
          $subscription->setAutoRenew(false);
          break;

        case 18: // SUBSCRIPTION_CANCELLATION_SCHEDULED
          // Résiliation programmée en fin d'engagement.
          $subscription->setAutoRenew(false);
          break;

        case 20: // SUBSCRIPTION_PENDING_PURCHASE_CANCELED
          // Achat en attente annulé.
          $subscription->setStatus(
            SubscriptionStatus::SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED
          );
          break;

        default:
          $this->logger->warning('Unhandled Google RTDN type', [
            'type' => $notificationType
          ]);
          return;
      }

      $subscription->setUpdatedAt(new \DateTimeImmutable());
      $this->subscriptionRepository->save($subscription);
    } catch (\Throwable $e) {
      $this->logger->critical('Google RTDN unexpected error', [
        'error' => $e->getMessage(),
      ]);
    }
  }
}
