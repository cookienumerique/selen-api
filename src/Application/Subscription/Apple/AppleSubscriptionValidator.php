<?php

namespace App\Application\Subscription\Apple;

use App\Application\Auth\DecodeAppleJWT;
use App\Exception\InvalidSubscriptionException;
use App\Infrastructure\Apple\AppleTransactionInfo;

final class AppleSubscriptionValidator
{
  public function __construct(
    private DecodeAppleJWT $decodeAppleJWT,
    private string $appleBundleId,
    private string $environment,
  ) {}

  public function validate(string $receipt): AppleTransactionInfo
  {
    if (!is_string($receipt) || trim($receipt) === '') {
      throw new InvalidSubscriptionException('Invalid receipt format');
    }

    try {
      $decoded = $this->decodeAppleJWT->execute($receipt);
    } catch (\Throwable) {
      throw new InvalidSubscriptionException('Invalid receipt');
    }

    if ($decoded->bundleId !== $this->appleBundleId) {
      throw new InvalidSubscriptionException('Invalid bundle');
    }

    if (
      strtolower($decoded->environment) === 'sandbox'
      && $this->environment === 'production'
    ) {
      throw new InvalidSubscriptionException(
        'Sandbox receipt not allowed in production'
      );
    }

    return $decoded;
  }
}
