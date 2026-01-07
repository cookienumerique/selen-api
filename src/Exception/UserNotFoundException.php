<?php


namespace App\Exception;

use App\Exception\ApiException;

final class UserNotFoundException extends ApiException
{
  public function __construct(
    ?string $message = null
  ) {
    parent::__construct(
      'USER_NOT_FOUND',
      $message ?? 'User not found',
      404
    );
  }
}
