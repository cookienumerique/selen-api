<?php

namespace App\Controller;

use App\Application\Subscription\VerifyAndroidSubscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\MissingPayloadException;
use Psr\Log\LoggerInterface;

class SubscriptionController extends ApiController
{
  #[Route('/subscriptions/android', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function verifyAndroid(
    Request $request,
    UserInterface $user,
    VerifyAndroidSubscription $verifyAndroidSubscription,
    LoggerInterface $logger,
  ): JsonResponse {
    $data = $request->toArray();
    $productId = $data['productId'] ?? null;
    $purchaseToken = $data['purchaseToken'] ?? null;

    $logger->info('ANDROID SUBSCRIBE REQUEST', [
      'payload' => $data
    ]);

    if (!$productId) {
      throw new MissingPayloadException('productId');
    }

    if (!$purchaseToken) {
      throw new MissingPayloadException('purchaseToken');
    }

    $subscription = $verifyAndroidSubscription->execute(
      $user,
      $productId,
      $purchaseToken,
    );
    return $this->respondItem($subscription, JsonResponse::HTTP_CREATED);
  }
}
