<?php

namespace App\Entity;

use App\Repository\SalaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalaireRepository::class)]
class Salaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Utilisateur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Detail = null;

    #[ORM\Column]
    private ?float $Montant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?string
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(string $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

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

    public function getDetail(): ?string
    {
        return $this->Detail;
    }

    public function setDetail(?string $Detail): static
    {
        $this->Detail = $Detail;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->Montant;
    }

    public function setMontant(float $Montant): static
    {
        $this->Montant = $Montant;

        return $this;
    }
}
