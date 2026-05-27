<?php

namespace App\Exception\Subscription;

use App\Exception\ApiException;

final class PremiumFeatureRequiredException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'PREMIUM_FEATURE_REQUIRED',
      'This feature requires an active premium subscription.',
      403
    );
  }
}
