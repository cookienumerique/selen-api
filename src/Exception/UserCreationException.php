<?php

namespace App\Exception;

use App\Exception\ApiException;

final class UserCreationException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'USER_CREATION_ERROR',
      'Failed to create user.',
      500
    );
  }
}
