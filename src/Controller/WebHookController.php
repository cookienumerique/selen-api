<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Subscription\GooglePlayNotificationHandler;
use App\Application\Subscription\GoogleWebhook\GoogleWebhookAuthenticator;
use App\Application\Subscription\GoogleWebhook\GooglePubSubDecoder;
use App\Application\Subscription\GoogleWebhook\GoogleWebhookVerifier;
use Psr\Log\LoggerInterface;
use App\Application\Subscription\Apple\AppleWebhookHandler;
use App\Application\Subscription\AppleWebhook\AppleWebhookVerifier;
use App\Exception\InvalidSubscriptionException;

final class WebHookController extends ApiController
{
  #[Route('/webhooks/google-play', methods: ['POST'])]
  public function googlePlay(
    Request $request,
    GoogleWebhookAuthenticator $googleWebhookAuthenticator,
    GoogleWebhookVerifier $googleWebhookVerifier,
    GooglePubSubDecoder $googlePubSubDecoder,
    GooglePlayNotificationHandler $googlePlayNotificationHandler,
    LoggerInterface $logger
  ): JsonResponse {

    $idToken = $googleWebhookAuthenticator->execute($request);
    $logger->info('Google Webhook received', [
      'idToken' => $idToken
    ]);
    $googleWebhookVerifier->execute($idToken);
    $logger->info('Google Webhook verified');
    $developperNotification = $googlePubSubDecoder->execute($request);
    $logger->info('Google Webhook decoded');

    if (isset($developperNotification['subscriptionNotification'])) {
      $googlePlayNotificationHandler->handleSubscription($developperNotification['subscriptionNotification']);
    }
    if (isset($developperNotification['voidedPurchaseNotification'])) {
      $googlePlayNotificationHandler->handleVoidedPurchase($developperNotification['voidedPurchaseNotification']);
    }

    return new JsonResponse(null, 204);
  }

  #[Route('/webhooks/apple', methods: ['POST'])]
  public function apple(
    Request $request,
    AppleWebhookHandler $handleAppleWebhook,
    AppleWebhookVerifier $appleWebhookVerifier,
    LoggerInterface $logger
  ): JsonResponse {

    $body = $request->getContent();

    $logger->info('Apple webhook received', [
      'body' => $body
    ]);

    $payload = json_decode($body, true);

    if (!isset($payload['signedPayload'])) {
      throw new InvalidSubscriptionException('Missing signedPayload');
    }

    $signedPayload = $appleWebhookVerifier->execute($payload['signedPayload']);

    $logger->info('Apple Webhook signedPayload received', [
      'signedPayload' => $signedPayload
    ]);

    $handleAppleWebhook->execute($payload);

    return new JsonResponse(null, 204);
  }
}
