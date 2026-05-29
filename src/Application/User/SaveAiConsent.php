<?php

namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;

final class SaveAiConsent
{
    public const VALID_TRIGGERS = [
        'first_capsule',
        'first_journal',
        'settings',
    ];

    public function __construct(
        private UserRepository $repository,
    ) {}

    public function execute(User $user, bool $optin, string $trigger): void
    {
        $user
            ->setConsentAiOptin($optin)
            ->setConsentAiOptinAt(new \DateTimeImmutable())
            ->setConsentAiOptinTrigger($trigger);

        $this->repository->save($user);
    }
}
