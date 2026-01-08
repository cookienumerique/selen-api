<?php

namespace App\Application\Auth;

use App\Repository\UserRepository;
use App\Infrastructure\Auth\GoogleTokenVerifier;
use App\Entity\User;
use App\Domain\User\UserRole;

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
        $name = $payload['name'] ?? $payload['email'];
        $picture = $payload['picture'] ?? '';

        $user = $this->userRepository->findByGoogleId($googleId);

        try {
            if (!$user) {
                $user = (new User())
                    ->setGoogleId($googleId)
                    ->setEmail($email)
                    ->setRoles([UserRole::USER])
                    ->setName($name)
                    ->setPicture($picture);

                $user = $this->userRepository->create($user);
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to create user', $e->getCode());
        }
        return $user;
    }
}
