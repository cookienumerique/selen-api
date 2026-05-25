<?php

namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;

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
