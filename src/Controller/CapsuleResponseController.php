<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Application\CapsuleResponse\CreateCapsuleResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\MissingPayloadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\CapsuleResponse\ListCapsulesResponses;
use App\Application\CapsuleResponse\GetCapsuleResponse;
use App\Application\Capsule\GetCapsule;

class CapsuleResponseController extends ApiController
{

  #[Route('/capsules-response', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function create(
    Request $request,
    CreateCapsuleResponse $createCapsuleResponse,
    GetCapsule $getCapsule,
    UserInterface $author,
  ): JsonResponse {
    $data = $request->toArray();

    $capsuleId = $data['capsuleId'] ?? null;
    $response  = $data['response'] ?? null;

    if (!ctype_digit((string) $capsuleId)) {
      throw new MissingPayloadException('capsuleId');
    }

    $capsuleId = (int) $capsuleId;

    if (!is_string($response) || $response === '') {
      throw new MissingPayloadException('response');
    }

    $capsule = $getCapsule->execute($capsuleId);

    $createCapsuleResponse->execute($response, $author, $capsule);

    return $this->respondNoContent();
  }

  #[Route('/capsules-response', methods: ['GET'])]
  #[IsGranted('ROLE_USER')]
  public function list(
    ListCapsulesResponses $listCapsulesResponses,
    UserInterface $author,
  ): JsonResponse {
    $capsulesResponses = $listCapsulesResponses->execute($author);
    return $this->respondItems($capsulesResponses, JsonResponse::HTTP_OK);
  }

  #[Route('/capsules-response/{id}', methods: ['GET'])]
  #[IsGranted('ROLE_USER')]
  public function get(
    int $id,
    GetCapsuleResponse $getCapsuleResponse,
    UserInterface $author,
  ): JsonResponse {
    $capsuleResponse = $getCapsuleResponse->execute($id, $author);

    return $this->respondItem($capsuleResponse, JsonResponse::HTTP_OK);
  }
}
