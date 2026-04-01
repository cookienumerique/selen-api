<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use App\Application\Capsule\ListCapsules;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Capsule\ListCapsulesRanked;

final class CapsuleController extends ApiController
{
    #[Route('/capsules', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(
        Request $request,
        ListCapsules $listCapsules
    ): JsonResponse {
        $params = $request->query->all();
        $capsules = $listCapsules->execute($params);
        return $this->respondItems($capsules, JsonResponse::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/capsules/ranked', name: 'api_capsules_ranked', methods: ['GET'])]
    public function getRankedCapsules(ListCapsulesRanked $listCapsulesRanked): JsonResponse
    {
        $capsules = $listCapsulesRanked->execute();

        return $this->respondItems($capsules, JsonResponse::HTTP_OK);
    }
}
