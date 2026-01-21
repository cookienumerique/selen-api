<?php

namespace App\Application\CapsuleResponse;

use App\Entity\Capsule;
use App\Entity\CapsuleResponse;
use App\Entity\User;
use App\Repository\CapsuleResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\CapsuleAlreadyRespondedException;

class CreateCapsuleResponse
{
  public function __construct(
    private EntityManagerInterface $em,
    private CapsuleResponseRepository $repository,
  ) {}

  public function execute(string $response, User $user, Capsule $capsule): CapsuleResponse
  {
    if ($this->repository->isCapsuleAlreadyAnswered($user, $capsule)) {
      throw new CapsuleAlreadyRespondedException('Capsule already responded by this user.');
    }

    $capsuleResponse = (new CapsuleResponse())
      ->setAuthor($user)
      ->setCapsule($capsule)
      ->setResponse($response)
      ->setCreatedAt(new \DateTimeImmutable());

    $this->em->persist($capsuleResponse);
    $this->em->flush();

    return $capsuleResponse;
  }
}
