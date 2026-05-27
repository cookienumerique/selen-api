<?php

namespace App\Application\Subscription\Apple;

use App\Infrastructure\Apple\AppleTransactionInfo;
use App\Application\Auth\DecodeAppleJWT;
use App\Repository\SubscriptionRepository;
use App\Enum\Subscription\SubscriptionProvider;
use Psr\Log\LoggerInterface;

final class AppleWebhookHandler
{
  public function __construct(
    private DecodeAppleJWT $decodeAppleJWT,
    private SubscriptionRepository $subscriptionRepository,
    private LoggerInterface $logger,
  ) {}

  public function execute(array $payload): void
  {
    if (!isset($payload['signedPayload'])) {
      return;
    }

    // 1. Décodage du payload global (la "shape" du Webhook)
    // On utilise decode() pour avoir accès à ->notificationType et ->data
    $decodedPayload = $this->decodeAppleJWT->decode($payload['signedPayload']);

    // 2. Extraction et décodage des JWS imbriqués
    $transactionInfoDecoded = $this->decodeAppleJWT->decode($decodedPayload->data->signedTransactionInfo);

    $renewalInfoDecoded = null;
    if (isset($decodedPayload->data->signedRenewalInfo)) {
      $renewalInfoDecoded = $this->decodeAppleJWT->decode($decodedPayload->data->signedRenewalInfo);
    }

    // 3. Mapping vers notre DTO avec les infos du Webhook
    $appleInfo = AppleTransactionInfo::fromDecoded(
      $transactionInfoDecoded,
      $renewalInfoDecoded,
      $decodedPayload->notificationType ?? null, // On passe le type (ex: DID_RENEW)
      $decodedPayload->subtype ?? null          // On passe le subtype (ex: AUTO_RENEW_ENABLED)
    );

    // 4. Recherche de la souscription par Original Transaction ID
    $subscription = $this->subscriptionRepository->findOneBy([
      'provider' => SubscriptionProvider::APPLE,
      'providerSubscriptionId' => $appleInfo->originalTransactionId
    ]);

    if (!$subscription) {
      $this->logger->error('Apple Webhook: No subscription found for ID ' . $appleInfo->originalTransactionId);
      return;
    }

    $subscription
      ->setStatus($appleInfo->getStatus())
      ->setAutoRenew($appleInfo->autoRenewStatus)
      ->setExpiresAt((new \DateTimeImmutable())->setTimestamp($appleInfo->expiresDate / 1000))
      ->setUpdatedAt(new \DateTimeImmutable());

    // 6. Persistance
    $this->subscriptionRepository->save($subscription);
  }
}
