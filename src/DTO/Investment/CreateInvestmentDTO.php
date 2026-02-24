<?php

namespace App\DTO\Investment;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInvestmentDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\Positive]
    public float $investedValue;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Type('string')]
    #[Assert\Length(min: 5, max: 255)]
    public string $ownerEmail;

    #[Assert\Type('DateTimeImmutable')]
    #[Assert\LessThanOrEqual('now')]
    public ?DateTimeImmutable $creationDate = null;
}