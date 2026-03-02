<?php

namespace App\Application\Subscription\GoogleWebhook;

use Symfony\Component\HttpFoundation\Request;
use App\Exception\InvalidWebhookTokenException;

class GoogleWebhookAuthenticator
{
  public function execute(Request $request): string
  {
    $authHeader = $request->headers->get('Authorization');

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      throw new InvalidWebhookTokenException('Missing Authorization header');
    }

    $idToken = substr($authHeader, 7);
    return str_replace('Bearer ', '', $authHeader);
  }
}
