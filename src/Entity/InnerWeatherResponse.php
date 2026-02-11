<?php

namespace App\Entity;

use App\Repository\InnerWeatherResponseRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Contract\SerializableInterface;

#[ORM\Entity(repositoryClass: InnerWeatherResponseRepository::class)]
#[ORM\Table(
    name: 'inner_weather_response',
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'uniq_inner_weather_per_day',
            columns: ['author_id', 'day']
        )
    ]
)]
class InnerWeatherResponse implements SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?InnerWeather $innerWeather = null;

    #[ORM\ManyToOne(inversedBy: 'innerWeatherResponses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $day = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->day = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInnerWeather(): ?InnerWeather
    {
        return $this->innerWeather;
    }

    public function setInnerWeather(?InnerWeather $innerWeather): static
    {
        $this->innerWeather = $innerWeather;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDay(): ?\DateTimeImmutable
    {
        return $this->day;
    }

    public function setDay(\DateTimeImmutable $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'innerWeather' => $this->innerWeather->serialize(),
            'author' => $this->author->serialize(),
            'day' => $this->day->format('Y-m-d'),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
