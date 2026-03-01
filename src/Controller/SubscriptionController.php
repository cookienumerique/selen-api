<?php

namespace App\Controller;

use App\Application\Subscription\VerifyAndroidSubscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use App\Application\Subscription\Apple\SubscribeWithApple;
use App\Application\Subscription\Apple\AppleSubscriptionValidator;
use App\Application\Subscription\Google\GoogleSubscriptionVerifier;
use App\Application\Subscription\Google\SubscribeWithGoogle;
use App\Application\Subscription\Google\GoogleSubscriptionValidator;

class SubscriptionController extends ApiController
{
  #[Route('/subscriptions/android', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function subscribeAndroid(
    Request $request,
    UserInterface $user,
    GoogleSubscriptionValidator $googleSubscriptionValidator,
    GoogleSubscriptionVerifier $googleSubscriptionVerifier,
    SubscribeWithGoogle $subscribeWithGoogle,
    LoggerInterface $logger,
  ): JsonResponse {
    $data = $request->toArray();
    $productId = $data['productId'] ?? null;
    $purchaseToken = $data['purchaseToken'] ?? null;
    $logger->info('Google subscription request', [
      'payload' => $data
    ]);

    // Verify the payload
    $googleSubscriptionValidator->execute($data);

    // Verify the subscription
    $googlePurchase = $googleSubscriptionVerifier->execute($productId, $purchaseToken);

    // Subscribe the user
    $subscription = $subscribeWithGoogle->execute($user, $googlePurchase, $purchaseToken);

    return $this->respondItem($subscription, JsonResponse::HTTP_CREATED);
  }

  #[Route('/subscriptions/apple', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function subscribeApple(
    Request $request,
    UserInterface $user,
    SubscribeWithApple $subscribeWithApple,
    AppleSubscriptionValidator $appleSubscriptionValidator,
    LoggerInterface $logger,

  ): JsonResponse {

    $data = $request->toArray();
    $logger->info('Apple subscription request', [
      'payload' => $data
    ]);

    $appleTransactionInfo = $appleSubscriptionValidator->validate($data['receipt']);

    $subscription = $subscribeWithApple->execute($user, $appleTransactionInfo);

    return $this->respondItem($subscription, JsonResponse::HTTP_OK);
  }
}
