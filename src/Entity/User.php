<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use App\Domain\User\UserRole;
use App\Domain\User\SignupIntent;
use App\Contract\SerializableInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, SerializableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $uid;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $appleId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    // Consentement RGPD aux données de santé (art. 9), recueilli à l'écran de consentement de l'onboarding.
    // Tant que ce champ est null, l'onboarding n'est pas validé et l'accès à l'app reste bloqué.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $consentAt = null;

    // Version du texte de consentement accepté (ex : "1.0"). Sert à redemander le consentement si le texte évolue.
    #[ORM\Column(length: 16, nullable: true)]
    private ?string $consentVersion = null;

    // Opt-in IA OpenAI pour la "réponse de la lune". null = pas encore demandé, true = accepté, false = refusé.
    // Optionnel : son refus n'empêche pas d'utiliser le reste de l'app (météo, capsules, journal, calendrier).
    #[ORM\Column(nullable: true)]
    private ?bool $consentAiOptin = null;

    // Intention déclarée à l'inscription (écran "Qu'est-ce qui t'amène ici ?"). Voir l'enum SignupIntent pour les valeurs et leur libellé français.
    #[ORM\Column(length: 32, nullable: true, enumType: SignupIntent::class)]
    private ?SignupIntent $signupIntent = null;

    // Texte libre saisi uniquement si signupIntent vaut "autre" (max 200 caractères). null dans tous les autres cas.
    #[ORM\Column(length: 200, nullable: true)]
    private ?string $signupIntentOther = null;

    // Date d'enregistrement de l'intention. Permet de mesurer le délai entre l'inscription et la réponse.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $signupIntentAt = null;

    // Version de l'onboarding suivi par l'utilisatrice (ex : "1.26"). Sert à comparer les cohortes entre versions d'onboarding.
    #[ORM\Column(length: 8, nullable: true)]
    private ?string $onboardingVersion = null;

    // Date du choix opt-in IA (accepté ou refusé). Va de pair avec consentAiOptin.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $consentAiOptinAt = null;

    // Écran qui a déclenché la demande d'opt-in IA : first_capsule, first_journal ou settings.
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $consentAiOptinTrigger = null;

    public function __construct()
    {
        $this->uid = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
        $this->roles = [UserRole::USER];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): Uuid
    {
        return $this->uid;
    }

    public function setUid(Uuid $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles ?: [UserRole::USER];
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(string $googleId): static
    {
        $this->googleId = $googleId;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'uid' => $this->uid->toRfc4122(),
            'email' => $this->email,
            'roles' => $this->roles,
            'firstName' => $this->firstName,
            'name' => $this->name,
            'picture' => $this->picture,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
            'consentAiOptin' => $this->consentAiOptin,
            'consentAt' => $this->consentAt?->format(DATE_ATOM),
            'signupIntent' => $this->signupIntent?->value,
            'onboardingVersion' => $this->onboardingVersion,
        ];
    }

    public function getConsentAt(): ?\DateTimeImmutable
    {
        return $this->consentAt;
    }

    public function setConsentAt(?\DateTimeImmutable $consentAt): static
    {
        $this->consentAt = $consentAt;

        return $this;
    }

    public function getConsentVersion(): ?string
    {
        return $this->consentVersion;
    }

    public function setConsentVersion(?string $consentVersion): static
    {
        $this->consentVersion = $consentVersion;

        return $this;
    }

    public function getConsentAiOptin(): ?bool
    {
        return $this->consentAiOptin;
    }

    public function setConsentAiOptin(?bool $consentAiOptin): static
    {
        $this->consentAiOptin = $consentAiOptin;

        return $this;
    }

    public function hasGivenAiConsent(): bool
    {
        return $this->consentAiOptin === true;
    }

    public function getAppleId(): ?string
    {
        return $this->appleId;
    }

    public function setAppleId(?string $appleId): static
    {
        $this->appleId = $appleId;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSignupIntent(): ?SignupIntent
    {
        return $this->signupIntent;
    }

    public function setSignupIntent(?SignupIntent $signupIntent): static
    {
        $this->signupIntent = $signupIntent;

        return $this;
    }

    public function getSignupIntentOther(): ?string
    {
        return $this->signupIntentOther;
    }

    public function setSignupIntentOther(?string $signupIntentOther): static
    {
        $this->signupIntentOther = $signupIntentOther;

        return $this;
    }

    public function getSignupIntentAt(): ?\DateTimeImmutable
    {
        return $this->signupIntentAt;
    }

    public function setSignupIntentAt(?\DateTimeImmutable $signupIntentAt): static
    {
        $this->signupIntentAt = $signupIntentAt;

        return $this;
    }

    public function getOnboardingVersion(): ?string
    {
        return $this->onboardingVersion;
    }

    public function setOnboardingVersion(?string $onboardingVersion): static
    {
        $this->onboardingVersion = $onboardingVersion;

        return $this;
    }

    public function getConsentAiOptinAt(): ?\DateTimeImmutable
    {
        return $this->consentAiOptinAt;
    }

    public function setConsentAiOptinAt(?\DateTimeImmutable $consentAiOptinAt): static
    {
        $this->consentAiOptinAt = $consentAiOptinAt;

        return $this;
    }

    public function getConsentAiOptinTrigger(): ?string
    {
        return $this->consentAiOptinTrigger;
    }

    public function setConsentAiOptinTrigger(?string $consentAiOptinTrigger): static
    {
        $this->consentAiOptinTrigger = $consentAiOptinTrigger;

        return $this;
    }
}
