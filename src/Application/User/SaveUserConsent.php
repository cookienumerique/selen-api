<?php

namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;

final class SaveUserConsent
{
    public function __construct(
        private UserRepository $repository,
        private string $consentVersion
    ) {}

    public function execute(User $user, bool $aiOptin): User
    {
        $user
            ->setConsentAt(new \DateTimeImmutable())
            ->setConsentVersion($this->consentVersion)
            ->setConsentAiOptin($aiOptin);

        $this->repository->save($user);

        return $user;
    }
}
