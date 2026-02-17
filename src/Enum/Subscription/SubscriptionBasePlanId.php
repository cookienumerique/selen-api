<?php

namespace App\Enum\Subscription;

enum SubscriptionBasePlanId: string
{
  case SELEN_PREMIUM_MONTHLY = 'selen-premium-monthly';
  case SELEN_PREMIUM_YEARLY = 'selen-premium-yearly';
}
