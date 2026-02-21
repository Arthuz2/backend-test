<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthCheckController extends AbstractController
{
    #[Route('/', name: 'api_health_check', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            "status" => "ok",
            "datetime" => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }
}