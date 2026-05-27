<?php

namespace App\Application\Subscription\GoogleWebhook;

use Psr\Log\LoggerInterface;
use App\Exception\InvalidWebhookTokenException;
use Google\Auth\AccessToken;

class GoogleWebhookVerifier
{
  public function __construct(
    private LoggerInterface $logger
  ) {}

  /**
   * @throws InvalidWebhookTokenException
   */
  public function execute(string $idToken): array
  {

    try {

      $verifier = new AccessToken();
      $payload = $verifier->verify($idToken, [
        'audience' => $_ENV['API_URL'] . '/webhooks/google-play',
        'issuer' => 'https://accounts.google.com'
      ]);

      if (!$payload) {
        throw new InvalidWebhookTokenException('Invalid ID Token signature or expired');
      }

      $email = 'selen-backend@selen-app-officiel-c652b.iam.gserviceaccount.com';
      if ($payload['email'] !== $email) {
        throw new InvalidWebhookTokenException("Email mismatch. Expected: $email, Got: {$payload['email']}");
      }

      $expectedAudience = $_ENV['API_URL'] . '/webhooks/google-play';
      if ($payload['aud'] !== $expectedAudience) {
        throw new InvalidWebhookTokenException("Audience mismatch. Expected: $expectedAudience, Got: {$payload['aud']}");
      }

      return $payload;
    } catch (\Exception $e) {
      $this->logger->error('Webhook authentication failed', ['error' => $e->getMessage()]);
      throw new InvalidWebhookTokenException($e->getMessage());
    }
  }
}
