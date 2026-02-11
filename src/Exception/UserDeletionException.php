<?php

namespace App\Exception;


final class UserDeletionException extends ApiException
{
  public function __construct()
  {
    parent::__construct(
      'USER_DELETION_ERROR',
      500,
      500
    );
  }
}
