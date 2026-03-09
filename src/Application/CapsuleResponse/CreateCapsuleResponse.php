<?php

namespace App\Application\CapsuleResponse;

use App\Entity\Capsule;
use App\Entity\CapsuleResponse;
use App\Entity\User;
use App\Repository\CapsuleResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\CapsuleAlreadyRespondedException;
use App\Application\Ai\GenerateAIResponse;

class CreateCapsuleResponse
{
  public function __construct(
    private EntityManagerInterface $em,
    private CapsuleResponseRepository $repository,
    private GenerateAIResponse $generateAIResponse
  ) {}

  public function execute(string $response, User $user, Capsule $capsule): CapsuleResponse
  {
    if ($this->repository->isCapsuleAlreadyAnswered($user, $capsule)) {
      throw new CapsuleAlreadyRespondedException('Capsule already responded by this user.');
    }

    $aiResponse = $this->generateAIResponse->execute($capsule->getContent(), $response, $user);

    $capsuleResponse = (new CapsuleResponse())
      ->setAuthor($user)
      ->setCapsule($capsule)
      ->setResponse($response)
      ->setAiResponse($aiResponse)
      ->setCreatedAt(new \DateTimeImmutable());

    $this->em->persist($capsuleResponse);
    $this->em->flush();

    return $capsuleResponse;
  }
}
