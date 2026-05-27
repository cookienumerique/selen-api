<?php

namespace App\Application\Subscription\Google;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Enum\Subscription\SubscriptionProvider;
use App\Enum\Subscription\SubscriptionProductId;
use App\Enum\Subscription\SubscriptionBasePlanId;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use Exception;
use Psr\Log\LoggerInterface;
use App\Enum\Subscription\SubscriptionStatus;
use App\Exception\InvalidSubscriptionException;

class SubscribeWithGoogle
{
  public function __construct(
    private SubscriptionRepository $subscriptionRepository,
    private LoggerInterface $logger,
  ) {}

  public function execute(
    User $user,
    SubscriptionPurchaseV2 $subscriptionPurchaseV2,
    string $providerSubscriptionId,
  ): Subscription {

    try {
      $lineItems = $subscriptionPurchaseV2->getLineItems();
      if (empty($lineItems)) {
        throw new InvalidSubscriptionException('No line items found in Google subscription');
      }

      $lineItem = $lineItems[0];

      $expiresAt = new \DateTimeImmutable($lineItem->getExpiryTime());
      $status = SubscriptionStatus::from($subscriptionPurchaseV2->getSubscriptionState());
      $productIdEnum = SubscriptionProductId::from($lineItem->getProductId());
      $basePlanIdEnum = SubscriptionBasePlanId::from($lineItem->getOfferDetails()?->getBasePlanId());
      $isAutoRenewingPlan = $lineItem->getAutoRenewingPlan()?->getAutoRenewEnabled() ?? false;

      // 1. On cherche si l'abonnement existe déjà
      $subscription = $this->subscriptionRepository->findOneBy([
        'providerSubscriptionId' => $providerSubscriptionId,
        'provider'      => SubscriptionProvider::GOOGLE,
      ]);

      if ($subscription && $subscription->getUser()->getId() !== $user->getId()) {
        throw new InvalidSubscriptionException(
          'Google subscription already linked to another user'
        );
      }

      // 2. Si non trouvé, on instancie
      if (!$subscription) {
        $subscription = new Subscription();
        $subscription
          ->setUser($user)
          ->setProvider(SubscriptionProvider::GOOGLE)
          ->setProviderSubscriptionId($providerSubscriptionId);
      }

      // 3. On met à jour les données (valable pour création ET mise à jour)
      $subscription
        ->setProductId($productIdEnum)
        ->setBasePlanId($basePlanIdEnum)
        ->setStatus($status)
        ->setExpiresAt($expiresAt)
        ->setAutoRenew($isAutoRenewingPlan)
        ->setUpdatedAt(new \DateTimeImmutable());

      $this->subscriptionRepository->save($subscription);

      return $subscription;
    } catch (Exception $e) {
      $this->logger->critical('Unexpected error during Google subscription verification', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      throw new InvalidSubscriptionException($e->getMessage());
    }
  }

  public function executeFromWebhook(
    SubscriptionPurchaseV2 $subscriptionPurchaseV2,
    string $providerSubscriptionId,
  ): Subscription {

    try {

      $lineItems = $subscriptionPurchaseV2->getLineItems();
      if (empty($lineItems)) {
        throw new InvalidSubscriptionException('No line items found in Google subscription');
      }

      $lineItem = $lineItems[0];

      $subscription = $this->subscriptionRepository->findOneBy([
        'providerSubscriptionId' => $providerSubscriptionId,
        'provider' => SubscriptionProvider::GOOGLE,
      ]);

      if (!$subscription) {
        $this->logger->warning('Webhook received for unknown Google subscription', [
          'providerSubscriptionId' => $providerSubscriptionId
        ]);

        throw new InvalidSubscriptionException(
          'Google subscription not found for webhook update'
        );
      }

      // --- Mapping Google → Domain ---

      $expiresAt = new \DateTimeImmutable($lineItem->getExpiryTime());

      $status = SubscriptionStatus::from(
        $subscriptionPurchaseV2->getSubscriptionState()
      );

      $productIdEnum = SubscriptionProductId::from(
        $lineItem->getProductId()
      );

      $basePlanIdEnum = SubscriptionBasePlanId::from(
        $lineItem->getOfferDetails()?->getBasePlanId()
      );

      $isAutoRenewingPlan = $lineItem
        ->getAutoRenewingPlan()?->getAutoRenewEnabled() ?? false;

      // --- Update only (idempotent) ---

      $subscription
        ->setProductId($productIdEnum)
        ->setBasePlanId($basePlanIdEnum)
        ->setStatus($status)
        ->setExpiresAt($expiresAt)
        ->setAutoRenew($isAutoRenewingPlan)
        ->setUpdatedAt(new \DateTimeImmutable());

      $this->subscriptionRepository->save($subscription);

      $this->logger->info('Google subscription updated via webhook', [
        'providerSubscriptionId' => $providerSubscriptionId,
        'status' => $status->value
      ]);

      return $subscription;
    } catch (Exception $e) {
      $this->logger->critical('Webhook Google subscription update failed', [
        'error' => $e->getMessage(),
      ]);

      throw new InvalidSubscriptionException($e->getMessage());
    }
  }
}
