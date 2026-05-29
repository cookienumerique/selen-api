<?php

namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;

// TODO (cleanup) : façon legacy d'enregistrer l'opt-in IA, vouée à disparaître.
// L'opt-in IA passe désormais par POST /users/consent-ai (SaveAiConsent), le consentement RGPD par POST /users/onboarding (SaveUserOnboarding).
// À supprimer une fois privacy-screen migré : cette classe + UserController::consent() côté API, use-save-consent.ts + son appel dans privacy-screen.tsx côté app.
final class SaveUserConsent
{
    public const VERSION = '1.0';

    public function __construct(
        private UserRepository $repository,
    ) {}

    public function execute(User $user, bool $aiOptin): void
    {
        $user
            ->setConsentAt(new \DateTimeImmutable())
            ->setConsentVersion(self::VERSION)
            ->setConsentAiOptin($aiOptin);

        $this->repository->save($user);
    }
}
