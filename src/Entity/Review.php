<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: 'reviews')]
#[ORM\HasLifecycleCallbacks] // Miért: Automatikusan kezeli a létrehozási/módosítási dátumokat
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'A cégnév megadása kötelező.')]
    private ?string $companyName = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: 'Az értékelés megadása kötelező.')]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'Az értékelésnek 1 és 5 között kell lennie.')]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'A vélemény szövegének megadása kötelező.')]
    private ?string $reviewText = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Az email cím megadása kötelező.')]
    #[Assert\Email(message: 'Kérjük, valid email címet adj meg.')]
    private ?string $authorEmail = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    // --- Miért fontos lifecycle callback-eket használni? ---
    // Így a Controllerben vagy a Form-ban nem kell manuálisan kitölteni ezeket a mezőket.
    #[ORM\PrePersist]
    public function setInitialTimestamps(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdateTimestamp(): void
    {
        $this->updatedAt = new \DateTime();
    }

    // --- Getterek és Setterek ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewText(): ?string
    {
        return $this->reviewText;
    }

    public function setReviewText(string $reviewText): static
    {
        $this->reviewText = $reviewText;

        return $this;
    }

    public function getAuthorEmail(): ?string
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail(string $authorEmail): static
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
}
