<?php

namespace App\Exception;

use App\Exception\ApiException;

final class InvalidGoogleTokenException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'INVALID_GOOGLE_TOKEN',
      'Failed to authenticate with Google.',
      401
    );
  }
}
