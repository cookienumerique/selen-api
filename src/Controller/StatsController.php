<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Stats\CountOpenedCapsulesResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatsController extends ApiController
{

  #[Route('/stats/capsules-response/opened', methods: ['GET'])]
  #[IsGranted('PUBLIC_ACCESS')]
  public function getNbCapsulesOpened(
    CountOpenedCapsulesResponse $countOpenedCapsulesResponse,
  ): JsonResponse {
    $nbCapsules = $countOpenedCapsulesResponse->execute();
    return $this->json(['total' => $nbCapsules], JsonResponse::HTTP_OK);
  }
}
