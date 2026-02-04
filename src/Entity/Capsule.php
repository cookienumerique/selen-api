<?php

namespace App\Entity;

use App\Repository\CapsuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Contract\SerializableInterface;

#[ORM\Entity(repositoryClass: CapsuleRepository::class)]
class Capsule implements SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, CapsuleResponse>
     */
    #[ORM\OneToMany(targetEntity: CapsuleResponse::class, mappedBy: 'capsule')]
    private Collection $capsuleResponses;

    #[ORM\ManyToOne(inversedBy: 'capsules')]
    private ?SubThemeCapsule $subThemeCapsule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    public function __construct()
    {
        $this->capsuleResponses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'subThemeCapsule' => $this->subThemeCapsule?->serialize(),
        ];
    }

    /**
     * @return Collection<int, CapsuleResponse>
     */
    public function getCapsuleResponses(): Collection
    {
        return $this->capsuleResponses;
    }

    public function addCapsuleResponse(CapsuleResponse $capsuleResponse): static
    {
        if (!$this->capsuleResponses->contains($capsuleResponse)) {
            $this->capsuleResponses->add($capsuleResponse);
            $capsuleResponse->setCapsule($this);
        }

        return $this;
    }

    public function removeCapsuleResponse(CapsuleResponse $capsuleResponse): static
    {
        if ($this->capsuleResponses->removeElement($capsuleResponse)) {
            // set the owning side to null (unless already changed)
            if ($capsuleResponse->getCapsule() === $this) {
                $capsuleResponse->setCapsule(null);
            }
        }

        return $this;
    }

    public function getSubThemeCapsule(): ?SubThemeCapsule
    {
        return $this->subThemeCapsule;
    }

    public function setSubThemeCapsule(?SubThemeCapsule $subThemeCapsule): static
    {
        $this->subThemeCapsule = $subThemeCapsule;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
