<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Subscription\GooglePlayNotificationHandler;
use Psr\Log\LoggerInterface;
use App\Application\Subscription\Apple\HandleAppleWebhook;
use App\Exception\InvalidSubscriptionException;
use App\Application\Subscription\GoogleWebhook\GoogleWebhookAuthenticator;
use App\Application\Subscription\GoogleWebhook\GooglePubSubDecoder;
use App\Application\Subscription\GoogleWebhook\GoogleWebhookVerifier;
use Psr\Log\LoggerInterface;
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
    if (!$developperNotification['subscriptionNotification']) {
      return new JsonResponse(null, 204);
    }
    $googlePlayNotificationHandler->execute($developperNotification['subscriptionNotification']);

    return new JsonResponse(null, 204);
  }

  #[Route('/webhooks/apple', methods: ['POST'])]
  public function apple(
    Request $request,
    HandleAppleWebhook $handleAppleWebhook,
    LoggerInterface $logger
  ): JsonResponse {

    $content = $request->getContent();
    $payload = json_decode($content, true);

    $logger->info('Apple Webhook received', [
      'payload_keys' => array_keys($payload ?? [])
    ]);

    // Apple envoie un champ "signedPayload" qui contient tout le message en JWS
    if (!isset($payload['signedPayload'])) {
      throw new InvalidSubscriptionException('Missing signedPayload');
    }

    try {
      $handleAppleWebhook->execute($payload);

      $logger->info('Apple Webhook processed successfully');

      return new JsonResponse(null, 204);
    } catch (\Exception $e) {
      $logger->error('Apple Webhook processing failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return new JsonResponse(['error' => $e->getMessage()], 200);
    }
  }
}
