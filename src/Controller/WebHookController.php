<?php

namespace App\Controller;

use Google\Auth\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Subscription\GooglePlayNotificationHandler;
use Psr\Log\LoggerInterface;
use App\Application\Auth\DecodeAppleJWT;

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
        'audience' => $_ENV['API_URL'] . '/webhooks/google-play'
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

  #[Route('/webhooks/apple', methods: ['POST'])]
  public function apple(Request $request, DecodeAppleJWT $decodeAppleJWT, LoggerInterface $logger): JsonResponse
  {
    $payload = json_decode($request->getContent(), true);

    $logger->info('Apple WebHook request', [
      'payload' => json_encode($payload, true)
    ]);

    if (!isset($payload['signedPayload'])) {
      return new JsonResponse(['error' => 'Invalid payload'], 400);
    }

    $signedPayload = $payload['signedPayload'];

    try {
      $decodedPayload = $decodeAppleJWT->execute($signedPayload);

      $notificationType = $decodedPayload->notificationType ?? null;
      $data = $decodedPayload->data ?? null;

      if (!$data || !isset($data->signedTransactionInfo)) {
        return new JsonResponse(['error' => 'Missing transaction info'], 400);
      }

      $transactionInfo = $decodeAppleJWT->execute($data->signedTransactionInfo);

      // === INFOS IMPORTANTES ===
      $originalTransactionId = $transactionInfo->originalTransactionId ?? null;
      $productId = $transactionInfo->productId ?? null;
      $expiresDate = $transactionInfo->expiresDate ?? null;
      $environment = $transactionInfo->environment ?? null;

      // Ici pour l’instant on log
      dump([
        'notificationType' => $notificationType,
        'originalTransactionId' => $originalTransactionId,
        'productId' => $productId,
        'expiresDate' => $expiresDate,
        'environment' => $environment,
      ]);

      return new JsonResponse(null, 204);
    } catch (\Exception $e) {
      return new JsonResponse(['error' => $e->getMessage()], 400);
    }
  }
}
