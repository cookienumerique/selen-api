<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Application\User\DeleteMe;

final class UserController extends ApiController
{
    #[Route('/users/me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        return $this->json([
            'user' => $user->serialize()
        ]);
    }

    #[Route('/users/me', methods: 'DELETE')]
    #[IsGranted('ROLE_USER')]
    public function deleteMe(
        UserInterface $user,
        DeleteMe $deleteUser,
    ): JsonResponse {
        $deleteUser->execute($user);
        return $this->respondNoContent();
    }
}
