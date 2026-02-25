<?php

namespace App\Service\Investment;

use App\DTO\Investment\CreateInvestmentDTO;
use App\Entity\Investment;
use App\Exception\CreationDateInFutureException;
use App\Exception\InvalidIdException;
use App\Exception\InvestmentMustBePositiveException;
use App\Exception\InvestmentNotFoundException;
use App\Repository\InvestmentRepository;
use App\Service\Owner\OwnerService;
use Symfony\Component\Uid\Uuid;

class InvestmentService
{
    public function __construct(
        private OwnerService $ownerService,
        private InvestmentRepository $investmentRepository,
        private InvestmentCalculatorService $investmentCalculatorService,
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

    public function getInvestmentById(string $id): array
    {
        $id = Uuid::isValid($id) ? Uuid::fromString($id) : null;
        if (!$id) {
            throw new InvalidIdException();
        }

        $investment = $this->investmentRepository->find($id);
        if (!$investment) {
            throw new InvestmentNotFoundException();
        }

        $referenceDate = $investment->getWithdrawAt() ?? new \DateTimeImmutable();

        $months = $this->investmentCalculatorService->calculateMonths(
            $investment->getCreatedAt(),
            $referenceDate
        );

        $balance = $this->investmentCalculatorService->calculateBalance(
            $investment->getInvestedAmount(),
            $months
        );

        return [
            'id' => $investment->getId(),
            'ownerEmail' => $investment->getOwner()->getEmail(),
            'investedAmount' => (float) $investment->getInvestedAmount(),
            'expectedBalance' => $balance,
            'createdAt' => $investment->getCreatedAt()->format('Y-m-d'),
            'withdrawAt' => $investment->getWithdrawAt()?->format('Y-m-d'),
        ];
    }
}
