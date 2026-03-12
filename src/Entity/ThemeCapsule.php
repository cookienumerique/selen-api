<?php

namespace App\Entity;

use App\Repository\ThemeCapsuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemeCapsuleRepository::class)]
#[ORM\UniqueConstraint(name: 'uq_theme_capsule_code', columns: ['code'])]
class ThemeCapsule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SubThemeCapsule>
     */
    #[ORM\OneToMany(targetEntity: SubThemeCapsule::class, mappedBy: 'themeCapsule')]
    private Collection $subThemeCapsules;

    public function __construct()
    {
        $this->subThemeCapsules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, SubThemeCapsule>
     */
    public function getSubThemeCapsules(): Collection
    {
        return $this->subThemeCapsules;
    }

    public function addSubThemeCapsule(SubThemeCapsule $subThemeCapsule): static
    {
        if (!$this->subThemeCapsules->contains($subThemeCapsule)) {
            $this->subThemeCapsules->add($subThemeCapsule);
            $subThemeCapsule->setThemeCapsule($this);
        }

        return $this;
    }

    public function removeSubThemeCapsule(SubThemeCapsule $subThemeCapsule): static
    {
        if ($this->subThemeCapsules->removeElement($subThemeCapsule)) {
            if ($subThemeCapsule->getThemeCapsule() === $this) {
                $subThemeCapsule->setThemeCapsule(null);
            }
        }

        return $this;
    }
}
