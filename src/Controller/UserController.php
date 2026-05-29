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
use App\Application\User\SaveUserOnboarding;
use App\Application\User\SaveAiConsent;
use App\Domain\User\SignupIntent;
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

    // TODO (cleanup) : endpoint legacy d'opt-in IA. Remplacé par /users/consent-ai (opt-in) et /users/onboarding (consentement RGPD).
    // À retirer avec SaveUserConsent une fois privacy-screen migré côté app.
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

    #[Route('/users/onboarding', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function onboarding(
        Request $request,
        SaveUserOnboarding $saveUserOnboarding,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        $data = $request->toArray();

        $consentAccepted = $data['consentAccepted'] ?? null;
        if ($consentAccepted !== true) {
            throw new MissingPayloadException('consentAccepted');
        }

        $intentRaw = $data['intent'] ?? null;
        $intent = null;
        if ($intentRaw !== null) {
            if (!is_string($intentRaw) || ($intent = SignupIntent::tryFrom($intentRaw)) === null) {
                throw new MissingPayloadException('intent');
            }
        }

        $intentOther = $data['intentOther'] ?? null;
        if ($intentOther !== null && (!is_string($intentOther) || mb_strlen($intentOther) > 200)) {
            throw new MissingPayloadException('intentOther');
        }

        $saveUserOnboarding->execute($user, $intent, $intentOther);

        return $this->json(['user' => $user->serialize()]);
    }

    #[Route('/users/consent-ai', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function consentAi(
        Request $request,
        SaveAiConsent $saveAiConsent,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        $data = $request->toArray();

        $optin = $data['optin'] ?? null;
        if (!is_bool($optin)) {
            throw new MissingPayloadException('optin');
        }

        $trigger = $data['trigger'] ?? null;
        if (!is_string($trigger) || !in_array($trigger, SaveAiConsent::VALID_TRIGGERS, true)) {
            throw new MissingPayloadException('trigger');
        }

        $saveAiConsent->execute($user, $optin, $trigger);

        return $this->json(['user' => $user->serialize()]);
    }
}
