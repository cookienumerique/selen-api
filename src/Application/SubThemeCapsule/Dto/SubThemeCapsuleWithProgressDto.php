<?php

namespace App\Application\SubThemeCapsule\Dto;

use App\Entity\SubThemeCapsule;
use App\Contract\SerializableInterface;

final class SubThemeCapsuleWithProgressDto implements SerializableInterface
{
  public function __construct(
    public readonly SubThemeCapsule $subTheme,
    public readonly int $totalCapsules,
    public readonly int $answeredCapsules,
  ) {}

  public function isCompleted(): bool
  {
    if ($this->totalCapsules === 0) {
      return false;
    }
    return $this->answeredCapsules >= $this->totalCapsules;
  }

  public function serialize(): array
  {
    return [
      'subThemeCapsule' => $this->subTheme->serialize(),
      'totalCapsules' => $this->totalCapsules,
      'answeredCapsules' => $this->answeredCapsules,
      'isCompleted' => $this->isCompleted(),
    ];
  }
}
