<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ApiLogging
{
    protected function logApiError(\Throwable $e, string $context = ''): void
    {
        Log::error($context ?: 'API Error', [
            'message'   => $e->getMessage(),
            'exception' => get_class($e),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => $e->getTraceAsString(),
        ]);
    }

    protected function logApiInfo(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    protected function logApiWarning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }
}