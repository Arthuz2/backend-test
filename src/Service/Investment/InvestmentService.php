<?php

namespace App\Service\Investment;

use App\DTO\Investment\CreateInvestmentDTO;
use App\Entity\Investment;
use App\Exception\CreationDateInFutureException;
use App\Exception\InvestmentMustBePositiveException;
use App\Repository\InvestmentRepository;
use App\Service\Owner\OwnerService;

class InvestmentService
{
    public function __construct(
        private OwnerService $ownerService,
        private InvestmentRepository $investmentRepository,
    ) {}

    public function createInvestment(CreateInvestmentDTO $dto): Investment
    {
        $now = new \DateTimeImmutable();
        $creationDate = $dto->creationDate ?? $now;

        if ($dto->investedValue <= 0) {
            throw new InvestmentMustBePositiveException();
        }

        if ($creationDate > $now) {
            throw new CreationDateInFutureException();
        }

        $owner = $this->ownerService->createOrGetExistingOwner($dto->ownerEmail);

        $investment = new Investment();
        $investment->setInvestedAmount($dto->investedValue);
        $investment->setOwner($owner);
        $investment->setCreatedAt($creationDate);

        $this->investmentRepository->save($investment);

        return $investment;
    }
}