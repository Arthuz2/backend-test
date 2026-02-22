<?php

namespace App\Entity;

use App\Repository\InvestmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InvestmentRepository::class)]
class Investment
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Owner $owner = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $investedAmount = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $withdrawAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): static
    {
        $this->owner = $owner;

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

    public function getInvestedAmount(): ?string
    {
        return $this->investedAmount;
    }

    public function setInvestedAmount(string $investedAmount): static
    {
        $this->investedAmount = $investedAmount;

        return $this;
    }

    public function getWithdrawAt(): ?\DateTimeImmutable
    {
        return $this->withdrawAt;
    }

    public function setWithdrawAt(?\DateTimeImmutable $withdrawAt): static
    {
        $this->withdrawAt = $withdrawAt;

        return $this;
    }
}
