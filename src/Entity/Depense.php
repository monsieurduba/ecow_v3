<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $utilisateur = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $detail = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeDepense $type = null;

    #[ORM\Column]
    private ?bool $isPerso = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?string
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(string $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $Detail): static
    {
        $this->detail = $Detail;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

        return $this;
    }

    public function getType(): ?TypeDepense
    {
        return $this->type;
    }

    public function setType(?TypeDepense $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isPerso(): ?bool
    {
        return $this->isPerso;
    }

    public function setIsPerso(bool $isPerso): static
    {
        $this->isPerso = $isPerso;

        return $this;
    }
}
