<?php

namespace App\Service\Investment;

use App\DTO\Investment\CreateInvestmentDTO;
use App\DTO\Investment\WithdrawInvestmentDTO;
use App\Entity\Investment;
use App\Exception\CreationDateInFutureException;
use App\Exception\InvalidIdException;
use App\Exception\InvestmentAlreadyWithdrawnException;
use App\Exception\InvestmentMustBePositiveException;
use App\Exception\InvestmentNotFoundException;
use App\Exception\OwnerEmailRequiredException;
use App\Exception\OwnerNotFoundException;
use App\Exception\WithdrawDateBeforeCreationDateException;
use App\Exception\WithdrawDateInFutureException;
use App\Repository\InvestmentRepository;
use App\Service\Owner\OwnerService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Uid\Uuid;

class InvestmentService
{
    public function __construct(
        private OwnerService $ownerService,
        private InvestmentRepository $investmentRepository,
        private InvestmentCalculatorService $investmentCalculatorService,
        private PaginatorInterface $paginator,
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

    public function withdrawInvestment(string $id, WithdrawInvestmentDTO $dto): array
    {
        $id = Uuid::isValid($id) ? Uuid::fromString($id) : null;
        if (!$id) {
            throw new InvalidIdException();
        }

        $investment = $this->investmentRepository->find($id);
        if (!$investment) {
            throw new InvestmentNotFoundException();
        }

        if ($investment->getWithdrawAt()) {
            throw new InvestmentAlreadyWithdrawnException();
        }

        if ($dto->withdrawDate < $investment->getCreatedAt()) {
            throw new WithdrawDateBeforeCreationDateException();
        }

        if ($dto->withdrawDate > new \DateTimeImmutable()) {
            throw new WithdrawDateInFutureException();
        }

        $months = $this->investmentCalculatorService->calculateMonths(
            $investment->getCreatedAt(),
            $dto->withdrawDate
        );

        $balance = $this->investmentCalculatorService->calculateBalance(
            $investment->getInvestedAmount(),
            $months
        );

        $finalBalance = $this->investmentCalculatorService->calculateTax(
            investedAmount: $investment->getInvestedAmount(),
            balance: $balance,
            startDate: $investment->getCreatedAt(),
            endDate: $dto->withdrawDate
        );

        $investment->setWithdrawAt($dto->withdrawDate);
        $this->investmentRepository->save($investment);

        return [
            "initialAmount" => (float) $investment->getInvestedAmount(),
            "grossBalance" => $balance,
            "gain" => round($balance - $investment->getInvestedAmount(), 2),
            "tax" => round($balance - $finalBalance, 2),
            "finalWithdrawAmount" => $finalBalance
        ];
    }

    public function listInvestmentByOwner(string $ownerEmail, int $page, int $limit): array
    {
        if (!$ownerEmail) {
            throw new OwnerEmailRequiredException();
        }

        $owner = $this->ownerService->getOwnerByEmail($ownerEmail);
        if (!$owner) {
            throw new OwnerNotFoundException();
        }

        $qb = $this->investmentRepository->createFilteredQuery($ownerEmail);

        $pagination = $this->paginator->paginate(
            $qb,
            $page,
            $limit
        );

        $investments = [];
        foreach ($pagination->getItems() as $investment) {
            $investments[] = [
                'id' => $investment->getId(),
                'investedAmount' => (float) $investment->getInvestedAmount(),
                'createdAt' => $investment->getCreatedAt()->format('Y-m-d'),
                'withdrawAt' => $investment->getWithdrawAt()?->format('Y-m-d'),
            ];
        }

        return [
            'data' => $investments,
            'meta' => [
                'page' => $pagination->getCurrentPageNumber(),
                'limit' => $limit,
                'total' => $pagination->getTotalItemCount(),
                'totalPages' => (int) ceil($pagination->getTotalItemCount() / $limit),
            ],
        ];
    }
}