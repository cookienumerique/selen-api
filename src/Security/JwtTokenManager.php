<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class JwtTokenManager
{
  public function __construct(
    private JWTTokenManagerInterface $inner
  ) {}

  public function create(User $user): string
  {
    return $this->inner->createFromPayload($user, [
      'uid' => $user->getUid()->toRfc4122(),
      'email' => $user->getEmail(),
      'roles' => $user->getRoles(),
    ]);
  }
}
