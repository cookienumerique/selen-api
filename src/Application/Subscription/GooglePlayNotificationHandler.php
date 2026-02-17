<?php

namespace App\Application\Subscription;

use App\Repository\UserRepository;

class GooglePlayNotificationHandler
{
  public function __construct(
    private VerifyAndroidSubscription $verifyAndroidSubscription,
    private UserRepository $userRepository
  ) {}

  public function execute(array $payload): void
  {
    // Pub/Sub envoie le message encodé en base64
    $messageData = base64_decode($payload['message']['data']);
    $notification = json_decode($messageData, true);

    if (!isset($notification['subscriptionNotification'])) {
      return;
    }

    $subscription = $notification['subscriptionNotification'];

    $purchaseToken = $subscription['purchaseToken'];
    $productCode = $subscription['subscriptionId'];

    $user = $this->userRepository->findOneByEmail('test@selen.app');

    $this->verifyAndroidSubscription->execute(
      $user,
      $productCode,
      $purchaseToken
    );
  }
}
