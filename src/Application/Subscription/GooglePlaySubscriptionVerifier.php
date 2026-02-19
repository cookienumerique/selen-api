<?php

namespace App\Application\Subscription;

use Google\Client;
use Google\Service\AndroidPublisher;
use App\Exception\InvalidSubscriptionException;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;
use Google\Exception as GoogleException;
use App\Enum\Subscription\SubscriptionBasePlanId;

class GooglePlaySubscriptionVerifier
{
  private AndroidPublisher $androidPublisherService;
  private string $packageName;
  public function __construct(string $googleAuthConfigPath, string $packageName)
  {
    $client = new Client();
    $client->setAuthConfig($googleAuthConfigPath);
    $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

    $this->androidPublisherService = new AndroidPublisher($client);
    $this->packageName = $packageName;
  }

  public function verify(
    string $productId,
    string $purchaseToken
  ): SubscriptionPurchaseV2 {
    // // --- MODE DEBUG / MOCK ---
    // if ($purchaseToken === 'debug') {
    //   return $this->createMockSubscription($productId);
    // }

    try {
      // Récupération de l'abonnement via l'API V2
      $subscription = $this->androidPublisherService
        ->purchases_subscriptionsv2
        ->get($this->packageName, $purchaseToken);
    } catch (GoogleException $e) {
      throw new InvalidSubscriptionException("Google API Error: " . $e->getMessage());
    }

    // 1. Vérification du statut
    if ($subscription->getSubscriptionState() !== 'SUBSCRIPTION_STATE_ACTIVE') {
      throw new InvalidSubscriptionException("Subscription not active.");
    }

    $lineItems = $subscription->getLineItems();
    if (empty($lineItems)) {
      throw new InvalidSubscriptionException("No line items found.");
    }

    $lineItem = $lineItems[0];

    // 2. Sécurité : Vérifier que le ProductId correspond
    if ($lineItem->getProductId() !== $productId) {
      throw new InvalidSubscriptionException("Product ID mismatch.");
    }

    if ($lineItem->getExpiryTime() === null) {
      throw new InvalidSubscriptionException("Missing expiry time.");
    }

    return $subscription;
  }

  /**
   * Crée une réponse fictive pour les tests
   */
  private function createMockSubscription(string $productId): SubscriptionPurchaseV2
  {
    $subscription = new SubscriptionPurchaseV2();
    $subscription->setSubscriptionState('SUBSCRIPTION_STATE_ACTIVE');

    // On crée le LineItem fictif
    $lineItem = new \Google\Service\AndroidPublisher\SubscriptionPurchaseLineItem();
    $lineItem->setProductId($productId);
    // Expiration dans 30 jours (en millisecondes pour Google)
    // Google expects the expiry time as an RFC3339 timestamp, not milliseconds.
    $lineItem->setExpiryTime((new \DateTimeImmutable('+30 days'))->format(DATE_RFC3339_EXTENDED));

    // On ajoute le BasePlanId (important pour ton Enum !)
    $offerDetails = new \Google\Service\AndroidPublisher\OfferDetails();
    $basePlanId = SubscriptionBasePlanId::SELEN_PREMIUM_MONTHLY->value;

    $offerDetails->setBasePlanId($basePlanId);
    $lineItem->setOfferDetails($offerDetails);

    $subscription->setLineItems([$lineItem]);

    return $subscription;
  }
}
