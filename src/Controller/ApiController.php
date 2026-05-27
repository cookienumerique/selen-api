<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Contract\SerializableInterface;
use InvalidArgumentException;

abstract class ApiController extends AbstractController
{
  protected function respondItem(SerializableInterface $item, int $status = JsonResponse::HTTP_OK): JsonResponse
  {
    return $this->json([
      'item' => $item->serialize(),
    ], $status);
  }

  protected function respondItems(array $items = [], int $status = JsonResponse::HTTP_OK): JsonResponse
  {
    foreach ($items as $item) {
      if (!$item instanceof SerializableInterface) {
        throw new InvalidArgumentException('All items must implement SerializableInterface.');
      }
    }
    return $this->json([
      'items' => array_map(
        static fn(SerializableInterface $item) => $item->serialize(),
        $items
      ),
    ], $status);
  }

  protected function respondNoContent(): JsonResponse
  {
    return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
  }
}
