<?php

namespace App\Exception;

final class CapsuleResponseNotFoundException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'CAPSULE_RESPONSE_NOT_FOUND',
      404,
      404
    );
  }
}
