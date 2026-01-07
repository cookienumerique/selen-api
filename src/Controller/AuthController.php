<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Auth\AuthenticateWithGoogle;
use App\Security\JwtTokenManager;
use App\Exception\MissingPayloadException;

final class AuthController extends AbstractController
{
    #[Route('/auth/google', methods: ['POST'])]
    public function google(
        Request $request,
        AuthenticateWithGoogle $auth,
        JwtTokenManager $jwt
    ): JsonResponse {
        
        $data = $request?->toArray() ?? [];

        if (!isset($data['idToken'])) {
            throw new MissingPayloadException('Missing required payload field: idToken');
        }
        $user = $auth->execute($data['idToken']);
        $token = $jwt->create($user);
        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'createdAt' => $user->getCreatedAt()->format(DATE_ATOM),
            ]
        ]);
    }
}
