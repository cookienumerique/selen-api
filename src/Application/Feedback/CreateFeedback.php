<?php

namespace App\Application\Feedback;

use App\Repository\FeedbackRepository;
use App\Entity\Feedback;
use Symfony\Bundle\SecurityBundle\Security;

class CreateFeedback
{
  public function __construct(
    private FeedbackRepository $feedbackRepository,
    private Security $security
  ) {}

  public function execute(array $data): Feedback
  {
    $user = $this->security->getUser();
    $rating = $data['rating'] ?? null;
    $context = $data['context'] ?? null;
    $contextId = $data['contextId'] ?? null;
    $comment = $data['comment'] ?? null;

    $feedback = (new Feedback())
      ->setAuthor($user)
      ->setComment($comment)
      ->setRating($rating)
      ->setContext($context)
      ->setContextId($contextId);

    return $this->feedbackRepository->save($feedback);
  }
}
