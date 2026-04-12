<?php

namespace App\Application\JournalEntry;

use App\Entity\User;
use App\Repository\JournalEntryRepository;
use App\Entity\JournalEntry;
use App\Application\Ai\GenerateAiResponseForJournalEntry;

class CreateJournalEntry
{
  public function __construct(
    private JournalEntryRepository $journalEntryRepository,
    private GenerateAiResponseForJournalEntry $generateAiResponseForJournalEntry
  ) {}

  public function execute(string $content, User $user): JournalEntry
  {
    $aiResponse = $this->generateAiResponseForJournalEntry->execute($content, $user);

    $journalEntry = (new JournalEntry())
      ->setAuthor($user)
      ->setContent($content)
      ->setAiResponse($aiResponse);

    $this->journalEntryRepository->save($journalEntry);

    return $journalEntry;
  }
}
