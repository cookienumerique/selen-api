<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\MissingPayloadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Feedback\CreateFeedback;

class FeedbackController extends ApiController
{

  #[Route('/feedbacks', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function create(
    Request $request,
    CreateFeedback $createFeedback,
  ): JsonResponse {

    $data = $request->toArray();

    $rating = $data['rating'] ?? null;
    $context = $data['context'] ?? null;
    $contextId = $data['contextId'] ?? null;

    if (!$rating) {
      throw new MissingPayloadException('rating');
    }

    if (!$context) {
      throw new MissingPayloadException('context');
    }

    if (!$contextId) {
      throw new MissingPayloadException('contextId');
    }

    $feedback = $createFeedback->execute($data);

    return $this->respondItem($feedback, JsonResponse::HTTP_OK);
  }
}
