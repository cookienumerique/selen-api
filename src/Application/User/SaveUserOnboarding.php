<?php

namespace App\Application\User;

use App\Entity\User;
use App\Domain\User\SignupIntent;
use App\Repository\UserRepository;

final class SaveUserOnboarding
{
    public const CONSENT_VERSION = '1.0';
    public const ONBOARDING_VERSION = '1.26';

    public function __construct(
        private UserRepository $repository,
    ) {}

    public function execute(User $user, ?SignupIntent $intent, ?string $intentOther): void
    {
        $now = new \DateTimeImmutable();

        // consentAt est la preuve RGPD de la date du premier recueil du consentement : on ne l'écrase jamais une fois posée.
        if ($user->getConsentAt() === null) {
            $user
                ->setConsentAt($now)
                ->setConsentVersion(self::CONSENT_VERSION);
        }

        $user->setOnboardingVersion(self::ONBOARDING_VERSION);

        if ($intent !== null) {
            $user
                ->setSignupIntent($intent)
                ->setSignupIntentOther($intent === SignupIntent::OTHER ? $intentOther : null)
                ->setSignupIntentAt($now);
        }

        $this->repository->save($user);
    }
}
