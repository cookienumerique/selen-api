<?php

namespace App\Application\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Infrastructure\Apple\AppleTransactionInfo;

class DecodeAppleJWT
{
  /**
   * Décode n'importe quel JWS signé par Apple (Transaction, Renewal ou Webhook).
   * @return \stdClass L'objet PHP brut contenu dans le jeton.
   */
  public function decode(string $jwt): \stdClass
  {
    // 1. Extraction du Header pour récupérer le certificat x5c
    [$headerB64] = explode('.', $jwt);
    $header = json_decode(base64_decode($headerB64), true);

    if (!isset($header['x5c'][0])) {
      throw new \Exception('Missing x5c certificate in Apple JWS');
    }

    // 2. Reconstruction du certificat public au format PEM
    $cert = "-----BEGIN CERTIFICATE-----\n" .
      chunk_split($header['x5c'][0], 64, "\n") .
      "-----END CERTIFICATE-----\n";

    $publicKey = openssl_pkey_get_public($cert);

    if (!$publicKey) {
      throw new \Exception('Invalid Apple certificate');
    }

    // 3. Décodage et vérification de la signature ES256
    // Retourne un stdClass avec les données Apple
    return JWT::decode($jwt, new Key($publicKey, 'ES256'));
  }

  /**
   * Utilitaire pour transformer un JWS de transaction directement en DTO.
   * Utilisé principalement lors du flux d'achat initial.
   */
  public function execute(string $jwt): AppleTransactionInfo
  {
    $decoded = $this->decode($jwt);
    return AppleTransactionInfo::fromDecoded($decoded);
  }
}
