<?php

namespace App\Application\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DecodeAppleJWT
{
  public function execute(string $jwt): object
  {
    [$headerB64, $payloadB64, $signatureB64] = explode('.', $jwt);

    $header = json_decode(base64_decode($headerB64), true);

    if (!isset($header['x5c'][0])) {
      throw new \Exception('Missing x5c certificate');
    }

    $cert = "-----BEGIN CERTIFICATE-----\n" .
      chunk_split($header['x5c'][0], 64, "\n") .
      "-----END CERTIFICATE-----\n";

    $publicKey = openssl_pkey_get_public($cert);

    if (!$publicKey) {
      throw new \Exception('Invalid certificate');
    }

    return JWT::decode($jwt, new Key($publicKey, 'ES256'));
  }
}
