<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Auth\AuthenticateWithGoogle;
use App\Security\JwtTokenManager;
use App\Exception\MissingPayloadException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\ApiController;

final class AuthController extends ApiController
{
    #[Route('/auth/google', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function google(
        Request $request,
        AuthenticateWithGoogle $auth,
        JwtTokenManager $jwt
    ): JsonResponse {

        $data = $request->toArray() ?? [];
        $idToken = $data['idToken'] ?? null;

        if (!is_string($idToken) || $idToken === '') {
            throw new MissingPayloadException('idToken');
        }
        $user = $auth->execute($data['idToken']);
        $token = $jwt->create($user);

        return $this->json([
            'token' => $token,
            'user' => $user->serialize()
        ], JsonResponse::HTTP_OK);
    }
}
