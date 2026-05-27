<?php

namespace App\Exception;


final class InvalidWebhookTokenException extends ApiException
{
  public function __construct(string $message)
  {
    parent::__construct(
      $message ?? 'Invalid subscription.',
      400,
      400
    );
  }
}
