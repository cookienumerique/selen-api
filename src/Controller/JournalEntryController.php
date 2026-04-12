<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\MissingPayloadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\JournalEntry\CreateJournalEntry;

class JournalEntryController extends ApiController
{

  #[Route('/journal-entry', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function create(
    Request $request,
    CreateJournalEntry $createJournalEntry,
    UserInterface $author,
  ): JsonResponse {
    $data = $request->toArray();

    $content = $data['content'] ?? null;

    if (!is_string($content)) {
      throw new MissingPayloadException('content');
    }

    $journalEntry = $createJournalEntry->execute($content, $author);

    return $this->respondItem($journalEntry, JsonResponse::HTTP_OK);
  }
}
