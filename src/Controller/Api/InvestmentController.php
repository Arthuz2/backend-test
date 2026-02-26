<?php

namespace App\Controller\Api;

use App\DTO\Investment\CreateInvestmentDTO;
use App\DTO\Investment\WithdrawInvestmentDTO;
use App\Exception\CreateInvestmentRequestException;
use App\Exception\CreationDateInFutureException;
use App\Exception\InvalidIdException;
use App\Exception\InvestmentAlreadyWithdrawnException;
use App\Exception\InvestmentMustBePositiveException;
use App\Exception\InvestmentNotFoundException;
use App\Exception\ListInvestmentsRequestException;
use App\Exception\OwnerNotFoundException;
use App\Exception\ValidationException;
use App\Exception\ViewInvestmentRequestException;
use App\Exception\WithdrawDateBeforeCreationDateException;
use App\Exception\WithdrawDateInFutureException;
use App\Exception\WithdrawInvestmentRequestException;
use App\Service\DTOValidatorService;
use App\Service\Investment\InvestmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/investments', name: 'api_investments_')]
final class InvestmentController extends AbstractController
{
    public function __construct(
        private InvestmentService $investmentService,
        private SerializerInterface $serializer,
        private DTOValidatorService $validator,
    ) {}

    #[Route(name: 'create', methods: ['POST'])]
    public function createInvestment(Request $request): JsonResponse
    {
        $data = $this->serializer->deserialize($request->getContent(), CreateInvestmentDTO::class, 'json');

        try {
            $this->validator->validate($data);

            $investment = $this->investmentService->createInvestment($data);

            return new JsonResponse([
                'message' => 'Investment created successfully',
                'data' => [
                    'id' => $investment->getId(),
                    'investedAmount' => $investment->getInvestedAmount(),
                    'ownerEmail' => $investment->getOwner()->getEmail(),
                    'createdAt' => $investment->getCreatedAt()->format('Y-m-d'),
                ],
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                    'details' => $e->getErrors(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($e instanceof InvestmentMustBePositiveException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($e instanceof CreationDateInFutureException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'error' => (new CreateInvestmentRequestException())->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'view', methods: ['GET'])]
    public function viewInvestmentById(string $id): JsonResponse
    {
        try {
            $result = $this->investmentService->getInvestmentById($id);
            return new JsonResponse($result, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof InvalidIdException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($e instanceof InvestmentNotFoundException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'error' => (new ViewInvestmentRequestException())->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/withdraw', name: 'withdraw', methods: ['POST'])]
    public function withdrawInvestment(string $id, Request $request): JsonResponse
    {
        $data = $this->serializer->deserialize($request->getContent(), WithdrawInvestmentDTO::class, 'json');

        try {
            $this->validator->validate($data);

            $result = $this->investmentService->withdrawInvestment($id, $data);

            return new JsonResponse($result, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                    'details' => $e->getErrors(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($e instanceof InvalidIdException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($e instanceof InvestmentNotFoundException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($e instanceof InvestmentAlreadyWithdrawnException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($e instanceof WithdrawDateBeforeCreationDateException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($e instanceof WithdrawDateInFutureException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'error' => (new WithdrawInvestmentRequestException())->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function listInvestments(Request $request): JsonResponse
    {
        $ownerEmail = $request->query->get('ownerEmail');
        $page = (int) max(1, $request->query->get('page', 1));
        $limit = (int) min(50, max(5, $request->query->get('limit', 10)));

        try {
            $result = $this->investmentService->listInvestmentByOwner(
                $ownerEmail,
                $page,
                $limit
            );

            return new JsonResponse($result, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof OwnerNotFoundException) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'error' => (new ListInvestmentsRequestException())->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
