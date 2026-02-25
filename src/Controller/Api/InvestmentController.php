<?php

namespace App\Controller\Api;

use App\DTO\Investment\CreateInvestmentDTO;
use App\Exception\CreateInvestmentRequestException;
use App\Exception\CreationDateInFutureException;
use App\Exception\InvalidIdException;
use App\Exception\InvestmentMustBePositiveException;
use App\Exception\InvestmentNotFoundException;
use App\Exception\ValidationException;
use App\Exception\ViewInvestmentRequestException;
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
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
