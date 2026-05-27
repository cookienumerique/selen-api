<?php

namespace App\Exception;


final class InvalidProductCodeException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'Invalid product code.',
      400,
      400
    );
  }
}
