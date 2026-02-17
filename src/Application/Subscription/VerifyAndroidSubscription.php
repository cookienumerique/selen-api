<?php

namespace App\Application\Subscription;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Enum\Subscription\SubscriptionProvider;
use App\Enum\Subscription\SubscriptionProductId;
use App\Enum\Subscription\SubscriptionBasePlanId;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use Exception;
use Psr\Log\LoggerInterface;
use App\Enum\Subscription\SubscriptionStatusAndroid;

class VerifyAndroidSubscription
{
  public function __construct(
    private SubscriptionRepository $subscriptionRepository,
    private GooglePlaySubscriptionVerifier $googlePlaySubscriptionVerifier,
    private LoggerInterface $logger,
  ) {}

  public function execute(
    User $user,
    string $productId,
    string $purchaseToken,
  ): Subscription {

    try {
    // Verification with Google Play
      /** @var SubscriptionPurchaseV2 $googlePurchase */
      $googlePurchase = $this->googlePlaySubscriptionVerifier->verify(
        $productId,
        $purchaseToken
      );

      $lineItems = $googlePurchase->getLineItems();
      $lineItem = $lineItems[0];
      $expiresAt = new \DateTimeImmutable($lineItem->getExpiryTime());
      $status = SubscriptionStatusAndroid::from($googlePurchase->getSubscriptionState());
      $productIdEnum = SubscriptionProductId::from($lineItem->getProductId());
      $basePlanIdEnum = SubscriptionBasePlanId::from($lineItem->getOfferDetails()?->getBasePlanId());
      $autoRenewingPlan = $lineItem->getAutoRenewingPlan();
      $isAutoRenew = $autoRenewingPlan !== null;
      // 1. On cherche si l'abonnement existe déjà (findOneBy et non findBy)
      $subscription = $this->subscriptionRepository->findOneBy([
        'purchaseToken' => $purchaseToken,
        'provider'      => SubscriptionProvider::GOOGLE,
      ]);

      // 2. Si non trouvé, on instancie
      if (!$subscription) {
        $subscription = new Subscription();
        $subscription
          ->setUser($user)
          ->setProvider(SubscriptionProvider::GOOGLE)
          ->setPurchaseToken($purchaseToken)
          ->setOriginalTransactionId($purchaseToken);
      }

      // 3. On met à jour les données (valable pour création ET mise à jour)
      $subscription
        ->setProductId($productIdEnum)
        ->setBasePlanId($basePlanIdEnum)
        ->setStatus($status)
        ->setExpiresAt($expiresAt)
        ->setAutoRenew($isAutoRenew)
        ->setUpdatedAt(new \DateTimeImmutable());

      $this->subscriptionRepository->save($subscription);

      return $subscription;
    } catch (Exception $e) {
      $this->logger->critical('Unexpected error during Android verification', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      throw $e;
    }
  }
}
