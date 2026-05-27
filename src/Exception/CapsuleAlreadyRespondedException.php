<?php

namespace App\Exception;


final class CapsuleAlreadyRespondedException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'Capsule already responded by this user.',
      409,
      409
    );
  }
}
