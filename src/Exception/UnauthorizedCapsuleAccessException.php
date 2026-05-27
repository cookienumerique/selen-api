<?php

namespace App\Exception;

final class UnauthorizedCapsuleAccessException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'UNAUTHORIZED_CAPSULE_ACCESS',
      403,
      403
    );
  }
}
