<?php

namespace App\Enum\Subscription;

enum SubscriptionBasePlanId: string
{
  case SELEN_PREMIUM_MONTHLY = 'selen-premium-monthly';
  case SELEN_PREMIUM_YEARLY = 'selen-premium-yearly';
  case SELEN_PREMIUM_MONTHLY_FOUNDER = 'selen-premium-monthly-founder';
  case SELEN_PREMIUM_YEARLY_FOUNDER = 'selen-premium-yearly-founder';

  /**
   * @description Convert Apple product ID to subscription base plan ID (e.g. selen_premium_monthly -> selen-premium-monthly)
   * @param string $productId Apple product ID
   * @return self|null
   */
  public static function fromAppleProductId(string $productId): ?self
  {
    return self::tryFrom(str_replace('_', '-', $productId));
  }
}
