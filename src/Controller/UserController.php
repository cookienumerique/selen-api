<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Exception\UserNotFoundException;
use App\Exception\MissingPayloadException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Application\User\DeleteMe;
use App\Application\User\SaveUserConsent;
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

    #[Route('/users/consent', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function consent(
        Request $request,
        SaveUserConsent $saveUserConsent,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        $data = $request->toArray();
        $aiOptin = $data['aiOptin'] ?? null;

        if (!is_bool($aiOptin)) {
            throw new MissingPayloadException('aiOptin');
        }

        $saveUserConsent->execute($user, $aiOptin);

        return $this->json(['user' => $user->serialize()]);
    }
}
