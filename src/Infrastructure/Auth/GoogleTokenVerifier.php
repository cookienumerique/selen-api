<?php

namespace App\Infrastructure\Auth;

use Google\Client;
use App\Exception\InvalidGoogleTokenException;

final class GoogleTokenVerifier
{
    public function __construct(
        private string $clientId
    ) {}

    public function verify(string $idToken): array
    {
        $client = new Client(['client_id' => $this->clientId]);
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            throw new InvalidGoogleTokenException();
        }

        return $payload;
    }
}
