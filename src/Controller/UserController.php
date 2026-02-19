<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Application\User\DeleteMe;
use App\Application\Subscription\GetSubscriptionsByUser;

final class UserController extends ApiController
{
    #[Route('/users/me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(GetSubscriptionsByUser $getSubscriptionsByUser): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }
        $subscriptions = $getSubscriptionsByUser->execute($user);

        return $this->json([
            'user' => $user->serialize(),
            'subscriptions' => array_map(fn($subscription) => $subscription->serialize(), $subscriptions),
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
