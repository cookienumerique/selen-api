<?php

namespace App\Entity;

use App\Repository\SubThemeCapsuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Contract\SerializableInterface;

#[ORM\Entity(repositoryClass: SubThemeCapsuleRepository::class)]
#[ORM\UniqueConstraint(name: 'uq_sub_theme_capsule_code', columns: ['code'])]
class SubThemeCapsule implements SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subThemeCapsules')]
    #[ORM\JoinColumn(name: 'theme_capsule_id', nullable: false)]
    private ?ThemeCapsule $themeCapsule = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Capsule>
     */
    #[ORM\OneToMany(targetEntity: Capsule::class, mappedBy: 'subThemeCapsule')]
    private Collection $capsules;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->capsules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThemeCapsule(): ?ThemeCapsule
    {
        return $this->themeCapsule;
    }

    public function setThemeCapsule(?ThemeCapsule $themeCapsule): static
    {
        $this->themeCapsule = $themeCapsule;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Capsule>
     */
    public function getCapsules(): Collection
    {
        return $this->capsules;
    }

    public function addCapsule(Capsule $capsule): static
    {
        if (!$this->capsules->contains($capsule)) {
            $this->capsules->add($capsule);
            $capsule->setSubThemeCapsule($this);
        }

        return $this;
    }

    public function removeCapsule(Capsule $capsule): static
    {
        if ($this->capsules->removeElement($capsule)) {
            // set the owning side to null (unless already changed)
            if ($capsule->getSubThemeCapsule() === $this) {
                $capsule->setSubThemeCapsule(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image,
        ];
    }
}
