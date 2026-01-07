<?php


namespace App\Exception;

use App\Exception\ApiException;

final class MissingPayloadException extends ApiException
{
  public function __construct(
    string $field,
    ?string $message = null
  ) {
    parent::__construct(
      'MISSING_PAYLOAD',
      $message ?? sprintf('Missing required payload field: %s', $field),
      400
    );
  }
}
