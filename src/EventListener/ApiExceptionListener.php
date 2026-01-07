<?php

namespace App\EventListener;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ApiExceptionListener
{
  public function __invoke(ExceptionEvent $event): void
  {
    $e = $event->getThrowable();

    if ($e instanceof ApiException) {
      $event->setResponse(
        new JsonResponse(
          [
            'code' => $e->getErrorCode(),
            'message' => $e->getMessage(),
          ],
          $e->getStatusCode()
        )
      );
      return;
    }

    $event->setResponse(
      new JsonResponse(
        [
          'code' => 500,
          'message' => $e->getMessage(),
        ],
        500
      )
    );
  }
}
