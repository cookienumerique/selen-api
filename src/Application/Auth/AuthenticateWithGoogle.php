<?php

namespace App\Application\Auth;

use App\Repository\UserRepository;
use App\Infrastructure\Auth\GoogleTokenVerifier;
use App\Entity\User;

final class AuthenticateWithGoogle
{
    public function __construct(
        private UserRepository $userRepository,
        private GoogleTokenVerifier $google
    ) {}

    public function execute(string $idToken): User
    {
        $payload = $this->google->verify($idToken);
        if (!$payload) {
            throw new \ErrorException('Failed to authenticate with Google', 400);
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $lastName = $payload['family_name'] ?? '';
        $firstName = $payload['given_name'];
        $picture = $payload['picture'] ?? '';

        $user = $this->userRepository->findByGoogleId($googleId);

        if (!$user && $email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
        }
        if (!$user) {
            $user = new User();
            $user->setGoogleId($googleId);
            $user->setEmail($email)
                ->setPicture($picture)
                ->setName($lastName)
                ->setFirstName($firstName);
        } elseif (!$user->getGoogleId()) {
            $user->setGoogleId($googleId);
        }

        if ($firstName && !$user->getFirstName()) {
            $user->setFirstName($firstName);
        }

        if ($lastName && !$user->getName()) {
            $user->setName($lastName);
        }

        $this->userRepository->create($user);
        return $user;
    }
}
