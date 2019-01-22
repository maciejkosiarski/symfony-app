<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponsible
{
    protected function getJsonResponse(string $json, int $status = 200): JsonResponse
    {
        if (0 === $status) {
            $status = 500;
        }

        return new JsonResponse($json, $status,  [], true);
    }
}