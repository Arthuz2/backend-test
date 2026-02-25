<?php

namespace App\DTO\Investment;

use Symfony\Component\Validator\Constraints as Assert;

class WithdrawInvestmentDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('DateTimeImmutable')]
    #[Assert\LessThanOrEqual('now')]
    public \DateTimeImmutable $withdrawDate;
}