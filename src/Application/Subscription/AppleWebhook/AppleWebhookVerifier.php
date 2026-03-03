<?php

namespace App\Application\Subscription\AppleWebhook;

use App\Exception\InvalidWebhookTokenException;

final class AppleWebhookVerifier
{
  /**
   * @return string
   * @throws \RuntimeException
   */
  public function execute(string $body): string
  {
    $payload = json_decode($body, true);

    if (!isset($payload['signedPayload'])) {
      throw new InvalidWebhookTokenException('Missing signedPayload');
    }

    return $payload['signedPayload'];
  }
}
