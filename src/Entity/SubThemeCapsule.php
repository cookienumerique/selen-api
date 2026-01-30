<?php

namespace App\Entity;

use App\Repository\SubThemeCapsuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubThemeCapsuleRepository::class)]
#[ORM\Table(
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'uniq_sub_theme_capsule_code',
            columns: ['theme_capsule_id', 'code']
        )
    ]
)]
class SubThemeCapsule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subThemeCapsules')]
    #[ORM\JoinColumn(name: 'theme_capsule_id', nullable: false)]
    private ?ThemeCapsule $themeCapsule = null;

    #[ORM\Column(length: 50)] // unique code for sub theme capsule
    private ?string $code = null;

    #[ORM\Column(length: 255)] // unique name for sub theme capsule
    private ?string $name = null;

    /**
     * @var Collection<int, Capsule>
     */
    #[ORM\OneToMany(targetEntity: Capsule::class, mappedBy: 'subThemeCapsule')]
    private Collection $capsules;

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
}
