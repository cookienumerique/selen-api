<?php

namespace App\Enum\Subscription;

enum SubscriptionProvider: string
{
  case MANUAL = 'manual';
  case APPLE = 'apple';
  case GOOGLE = 'google';
}
