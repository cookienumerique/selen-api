<?php

namespace App\Entity;

use App\Repository\CapsuleResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Contract\SerializableInterface;

#[ORM\Entity(repositoryClass: CapsuleResponseRepository::class)]
class CapsuleResponse implements SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $response = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aiResponse = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'capsuleResponses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Capsule $capsule = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): static
    {
        $this->response = $response;

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

    public function getCapsule(): ?Capsule
    {
        return $this->capsule;
    }

    public function setCapsule(?Capsule $capsule): static
    {
        $this->capsule = $capsule;

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

    public function getAiResponse(): ?string
    {
        return $this->aiResponse;
    }

    public function setAiResponse(?string $aiResponse): static
    {
        $this->aiResponse = $aiResponse;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'response' => $this->response,
            'aiResponse' => $this->aiResponse,
            'author' => $this->author->serialize(),
            'createdAt' => $this->createdAt,
            'capsule' => $this->capsule->serialize(),
        ];
    }
}
