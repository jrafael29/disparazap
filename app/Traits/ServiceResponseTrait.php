<?php

declare(strict_types=1);

namespace App\Traits;

trait ServiceResponseTrait
{
    protected function success(array $data, int $statusCode = 200): array
    {
        return [
            'success' => true,
            'data' => $data,
            'statusCode' => $statusCode
        ];
    }

    protected function error(string $message, int $statusCode = 400): array
    {
        return [
            'success' => false,
            'messagge' => $message,
            'statusCode' => $statusCode
        ];
    }
}
