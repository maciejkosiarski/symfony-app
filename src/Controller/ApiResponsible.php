<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponsible
{
    protected function getJsonResponse(string $json, int $status = 200): JsonResponse
    {
        return new JsonResponse($json, $status,  [], true);
    }
}