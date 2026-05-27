<?php

namespace App\Exception;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class NotFoundException extends ApiException
{
  public function __construct(string $message)
  {
    parent::__construct(
      'RESSOURCE_NOT_FOUND',
      $message ?? 'Resource does not exist',
      Response::HTTP_NOT_FOUND
    );
  }
}
