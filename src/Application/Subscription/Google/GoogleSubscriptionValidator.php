<?php

namespace App\Application\Subscription\Google;

use App\Exception\InvalidSubscriptionException;

final class GoogleSubscriptionValidator
{

  public function execute(array $data): void
  {
    $productId = $data['productId'] ?? null;
    $purchaseToken = $data['purchaseToken'] ?? null;

    if (!$productId) {
      throw new InvalidSubscriptionException('productId');
    }

    if (!$purchaseToken) {
      throw new InvalidSubscriptionException('purchaseToken');
    }
  }
}
