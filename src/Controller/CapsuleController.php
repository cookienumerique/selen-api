<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use App\Application\Capsule\ListCapsules;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\ApiController;

final class CapsuleController extends ApiController
{
    #[Route('/capsules', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(
        ListCapsules $listCapsuleService
    ): JsonResponse {
        $capsules = $listCapsuleService->execute();
        return $this->respondItems($capsules, JsonResponse::HTTP_OK);
    }
}
