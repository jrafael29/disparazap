<?php

declare(strict_types=1);

namespace App\Traits;

trait ServiceResponseTrait
{
    protected function successResponse(array $data, int $statusCode = 200): array
    {
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    protected function errorResponse(string $message, int $statusCode = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
