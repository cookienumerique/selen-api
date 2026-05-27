<?php


namespace App\Exception;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class MissingPayloadException extends ApiException
{
  public function __construct(
    string $field,
    ?string $message = null
  ) {
    parent::__construct(
      'MISSING_PAYLOAD',
      $message ?? sprintf('Missing required payload field: %s', $field),
      Response::HTTP_BAD_REQUEST
    );
  }
}
