<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\Subscription\SubscriptionProvider;
use App\Enum\Subscription\SubscriptionBasePlanId;
use App\Enum\Subscription\SubscriptionProductId;
use App\Contract\SerializableInterface;
use App\Enum\Subscription\SubscriptionStatus;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription implements SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(enumType: SubscriptionProvider::class)]
    private SubscriptionProvider $provider;

    #[ORM\Column(enumType: SubscriptionBasePlanId::class, nullable: true)]
    private ?SubscriptionBasePlanId $basePlanId;

    #[ORM\Column(enumType: SubscriptionProductId::class, nullable: true)]
    private ?SubscriptionProductId $productId;

    #[ORM\Column(enumType: SubscriptionStatus::class)]
    private SubscriptionStatus $status;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $autoRenew;

    #[ORM\Column(length: 255)]
    private ?string $providerSubscriptionId = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getProvider(): SubscriptionProvider
    {
        return $this->provider;
    }

    public function setProvider(SubscriptionProvider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getBasePlanId(): ?SubscriptionBasePlanId
    {
        return $this->basePlanId;
    }

    public function setBasePlanId(?SubscriptionBasePlanId $basePlanId): static
    {
        $this->basePlanId = $basePlanId;

        return $this;
    }

    public function getProductId(): ?SubscriptionProductId
    {
        return $this->productId;
    }

    public function setProductId(?SubscriptionProductId $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function setStatus(SubscriptionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function getAutoRenew(): bool
    {
        return $this->autoRenew;
    }
    public function setAutoRenew(bool $autoRenew): static
    {
        $this->autoRenew = $autoRenew;
        return $this;
    }

    public function isValid(): bool
    {
        return in_array($this->status, [
            SubscriptionStatus::SUBSCRIPTION_STATE_ACTIVE,
            SubscriptionStatus::SUBSCRIPTION_STATE_IN_GRACE_PERIOD,
        ], true)
            && $this->expiresAt > new \DateTimeImmutable();
    }

    public function getProviderSubscriptionId(): ?string
    {
        return $this->providerSubscriptionId;
    }

    public function setProviderSubscriptionId(string $providerSubscriptionId): static
    {
        $this->providerSubscriptionId = $providerSubscriptionId;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'provider' => $this->provider,
            'basePlanId' => $this->basePlanId,
            'productId' => $this->productId,
            'providerSubscriptionId' => $this->providerSubscriptionId,
            'status' => $this->status,
            'expiresAt' => $this->expiresAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'autoRenew' => $this->autoRenew,
        ];
    }
}
