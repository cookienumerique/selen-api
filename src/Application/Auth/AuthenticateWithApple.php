<?php

namespace App\Application\Auth;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Exception\MissingPayloadException;
use RuntimeException;

final class AuthenticateWithApple
{
    private const APPLE_KEYS_URL = 'https://appleid.apple.com/auth/keys';
    private const APPLE_ISSUER = 'https://appleid.apple.com';

    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function execute(
        string $identityToken,
        ?string $lastName,
        ?string $firstName
    ): User {
        if ($identityToken === '') {
            throw new MissingPayloadException('identityToken');
        }

        $jwks = $this->fetchAppleKeys();

        try {
            $decoded = JWT::decode(
                $identityToken,
                JWK::parseKeySet($jwks)
            );
        } catch (\Throwable $e) {
            throw new RuntimeException('Invalid Apple identity token', 401);
        }

        $payload = (array) $decoded;

        if (($payload['iss'] ?? null) !== self::APPLE_ISSUER) {
            throw new RuntimeException('Invalid Apple issuer', 401);
        }

        if (($payload['exp'] ?? 0) < time()) {
            throw new RuntimeException('Expired Apple token', 401);
        }

        if (!isset($payload['sub'])) {
            throw new RuntimeException('Invalid Apple token', 401);
        }

        $appleId = $payload['sub'];
        $email = $payload['email'] ?? null;

        $user = $this->userRepository->findOneBy(['appleId' => $appleId]);

        if (!$user && $email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
        }

        if (!$user) {
            $user = new User();
            $user->setAppleId($appleId);
            $user->setEmail($email);
        } elseif (!$user->getAppleId()) {
            $user->setAppleId($appleId);
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

    private function fetchAppleKeys(): array
    {
        $json = @file_get_contents(self::APPLE_KEYS_URL);

        if ($json === false) {
            throw new RuntimeException('Unable to fetch Apple public keys', 503);
        }

        $jwks = json_decode($json, true);

        if (!is_array($jwks)) {
            throw new RuntimeException('Invalid Apple public keys format', 503);
        }

        return $jwks;
    }
}
