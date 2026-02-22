<?php

namespace App\Enum\Subscription;

enum SubscriptionBasePlanId: string
{
  case SELEN_PREMIUM_MONTHLY = 'selen-premium-monthly';
  case SELEN_PREMIUM_YEARLY = 'selen-premium-yearly';
  case SELEN_PREMIUM_MONTHLY_FOUNDER = 'selen-premium-monthly-founder';
  case SELEN_PREMIUM_YEARLY_FOUNDER = 'selen-premium-yearly-founder';
}
