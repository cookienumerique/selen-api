<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\ApiController;
use App\Application\SubThemeCapsule\ListSubThemeCapsules;
use Symfony\Component\HttpFoundation\Request;

final class SubThemeController extends ApiController
{
    #[Route('/sub-theme-capsules', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(
        Request $request,
        ListSubThemeCapsules $listSubThemeCapsules
    ): JsonResponse {
        $params = $request->query->all();

        $subThemeCapsules = $listSubThemeCapsules->execute($params);
        return $this->respondItems($subThemeCapsules, JsonResponse::HTTP_OK);
    }
}
