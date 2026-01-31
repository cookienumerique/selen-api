<?php

namespace App\Exception;


final class InnerWeatherAlreadyRespondedException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'Inner weather already responded by this user.',
      409,
      409
    );
  }
}
