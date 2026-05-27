<?php

namespace App\Infrastructure\Apple;

use App\Enum\Subscription\SubscriptionStatus;

final class AppleTransactionInfo
{
  public function __construct(
    public readonly string $transactionId,
    public readonly string $originalTransactionId,
    public readonly string $productId,
    public readonly string $bundleId,
    public readonly int $purchaseDate,
    public readonly int $originalPurchaseDate,
    public readonly int $expiresDate,
    public readonly int $signedDate,
    public readonly string $environment,
    public readonly string $transactionReason,
    public readonly int $price,
    public readonly string $currency,
    public readonly ?string $webOrderLineItemId = null,
    public readonly ?string $subscriptionGroupIdentifier = null,
    public readonly ?string $type = null,
    public readonly ?string $inAppOwnershipType = null,
    public readonly ?string $deviceVerification = null,
    public readonly ?string $deviceVerificationNonce = null,
    public readonly ?string $storefront = null,
    public readonly ?string $storefrontId = null,
    public readonly ?string $appTransactionId = null,
    public readonly ?int $quantity = null,
    public readonly ?int $revocationDate = null,
    public readonly ?bool $autoRenewStatus = null, // Statut du renouvellement (via signedRenewalInfo)
    public readonly ?string $notificationType = null, // Type de notification Webhook (SUBSCRIBED, DID_RENEW, etc.)
    public readonly ?string $subtype = null, // S
  ) {}

  public static function fromDecoded(
    object $decoded,
    ?object $renewalInfo = null, // Reçu via signedRenewalInfo
    ?string $notificationType = null, // Reçu via le webhook uniquement
    ?string $subtype = null
  ): AppleTransactionInfo {
    return new AppleTransactionInfo(
      transactionId: $decoded->transactionId,
      originalTransactionId: $decoded->originalTransactionId,
      productId: $decoded->productId,
      bundleId: $decoded->bundleId,
      purchaseDate: $decoded->purchaseDate,
      originalPurchaseDate: $decoded->originalPurchaseDate,
      expiresDate: $decoded->expiresDate,
      signedDate: $decoded->signedDate,
      environment: $decoded->environment,
      transactionReason: $decoded->transactionReason,
      price: $decoded->price,
      currency: $decoded->currency,
      webOrderLineItemId: $decoded->webOrderLineItemId ?? null,
      subscriptionGroupIdentifier: $decoded->subscriptionGroupIdentifier ?? null,
      type: $decoded->type ?? null,
      inAppOwnershipType: $decoded->inAppOwnershipType ?? null,
      deviceVerification: $decoded->deviceVerification ?? null,
      deviceVerificationNonce: $decoded->deviceVerificationNonce ?? null,
      storefront: $decoded->storefront ?? null,
      storefrontId: $decoded->storefrontId ?? null,
      appTransactionId: $decoded->appTransactionId ?? null,
      quantity: $decoded->quantity ?? null,
      revocationDate: $decoded->revocationDate ?? null,
      autoRenewStatus: isset($renewalInfo->autoRenewStatus) ? $renewalInfo->autoRenewStatus === 1 : true, // 1 = ON, 0 = OFF
      notificationType: $notificationType,
      subtype: $subtype
    );
  }

  /**
   * @description Get the status of the subscription based on the Apple transaction info
   * @return SubscriptionStatus
   */
  public function getStatus(): SubscriptionStatus
  {
    $now = new \DateTimeImmutable();

    if (!$this->expiresDate) {
      return SubscriptionStatus::SUBSCRIPTION_STATE_EXPIRED;
    }

    $expiresAt = (new \DateTimeImmutable())
      ->setTimestamp($this->expiresDate / 1000);

    // 1️⃣ Refund / revoke
    if ($this->revocationDate !== null) {
      return SubscriptionStatus::SUBSCRIPTION_STATE_REVOKED;
    }

    // 2️⃣ Active
    if ($expiresAt > $now) {
      return SubscriptionStatus::SUBSCRIPTION_STATE_ACTIVE;
    }

    // 3️⃣ Expired (fallback)
    return SubscriptionStatus::SUBSCRIPTION_STATE_EXPIRED;
  }
}
