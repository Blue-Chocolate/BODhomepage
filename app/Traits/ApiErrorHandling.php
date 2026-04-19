<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ApiErrorHandling
{
    protected function handleApiError(\Throwable $e, string $message = 'An error occurred'): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }

        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: $message,
            ], $e->getStatusCode());
        }

        if ($e instanceof \RuntimeException) {
            $code = (int) $e->getCode();
            $status = ($code >= 400 && $code < 600) ? $code : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: $message,
            ], $status);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}