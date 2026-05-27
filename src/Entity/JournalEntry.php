<?php

namespace App\Entity;

use App\Repository\JournalEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Contract\SerializableInterface;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: JournalEntryRepository::class)]
class JournalEntry implements SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $aiResponse = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAiResponse(): ?string
    {
        return $this->aiResponse;
    }

    public function setAiResponse(string $aiResponse): static
    {
        $this->aiResponse = $aiResponse;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'author' => $this->author->serialize(),
            'aiResponse' => $this->aiResponse,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
