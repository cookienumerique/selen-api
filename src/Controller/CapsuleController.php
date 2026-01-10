<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Capsule\GetAllCapsules;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CapsuleController extends AbstractController
{
    #[Route('/capsules', methods: ['GET'])]
    // #[IsGranted('ROLE_USER')]
    public function list(
        GetAllCapsules $getAllCapsules
    ): JsonResponse {
        $capsules = $getAllCapsules->execute();

        return $this->json(
            [
                'capsules' => array_map(fn($capsule) => $capsule->serialize(), $capsules)
            ]
        );
    }
}
