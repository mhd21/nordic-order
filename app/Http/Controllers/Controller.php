<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function retry(callable $function, int $maxRetries = 3)
    {
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                return $function();
            } catch (\Exception $e) {
                $retryCount++;

                if ($retryCount >= $maxRetries) {
                    throw $e;
                }

                \Log::warning(
                    "Retrying operation. Attempt {$retryCount}: " .
                        $e->getMessage()
                );
            }
        }
    }
}
