<?php

namespace App\Controller;

use Google\Auth\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Subscription\GooglePlayNotificationHandler;
use Psr\Log\LoggerInterface;

final class WebHookController extends ApiController
{
  #[Route('/webhooks/google-play', methods: ['POST'])]
  public function googlePlay(
    Request $request,
    GooglePlayNotificationHandler $googlePlayNotificationHandler,
    LoggerInterface $logger
  ): JsonResponse {

    // 1️⃣ Vérification du JWT envoyé par Pub/Sub
    $authHeader = $request->headers->get('Authorization');
    $logger->info('Authorization header', [
      'authHeader' => json_encode($authHeader, true)
    ]);
    $logger->info('Request content', [
      'content' => $request->getContent()
    ]);
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      return new JsonResponse(['error' => 'Missing Authorization header'], 401);
    }

    $idToken = str_replace('Bearer ', '', $authHeader);
    $logger->info('Google Play WebHook request', [
      'idToken' => $idToken
    ]);
    try {
      $verifier = new AccessToken();

      $payload = $verifier->verify($idToken, [
        'audience' => 'https://api-selen.cookie-numerique.fr/webhooks/google-play'
      ]);

      if (!$payload) {
        throw new \Exception('Invalid token payload');
      }

      $issuer = $payload['iss'] ?? '';
      if (!in_array($issuer, ['https://accounts.google.com', 'accounts.google.com'])) {
        throw new \Exception('Invalid issuer');
      }
    } catch (\Exception $e) {
      $logger->error('Authentication failed', [
        'error' => $e->getMessage()
      ]);
      return new JsonResponse([
        'error' => 'Authentication failed',
        'details' => $e->getMessage()
      ], 403);
    }

    // 2️⃣ Décodage du payload Pub/Sub
    $json = $request->getContent();
    $notification = json_decode($json, true);
    $subscriptionNotification = $notification['subscriptionNotification'] ?? null;

    if (!$subscriptionNotification) {
      return new JsonResponse(['error' => 'Not a subscription notification'], 200);
    }
    $purchaseToken = $subscriptionNotification['purchaseToken'] ?? null;
    $productId = $subscriptionNotification['subscriptionId'] ?? null;
    $notificationType = $subscriptionNotification['notificationType'] ?? null;

    if (!$purchaseToken || !$productId) {
      return new JsonResponse(['error' => 'Missing purchaseToken'], 400);
    }

    $googlePlayNotificationHandler->execute(
      $purchaseToken,
      $productId,
      $notificationType
    );

    return new JsonResponse(null, 204);
  }
}
