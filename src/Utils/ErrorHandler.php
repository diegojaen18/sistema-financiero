<?php
// src/Utils/ErrorHandler.php

namespace App\Utils;

use App\Interfaces\ErrorHandlerInterface;

class ErrorHandler implements ErrorHandlerInterface
{
    private Logger $logger;

    public function __construct(?Logger $logger = null)
    {
        $this->logger = $logger ?? new Logger();
    }

    public function handleException(\Throwable $e): void
    {
        $this->logger->error('Uncaught exception', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        http_response_code(500);

        if (php_sapi_name() === 'cli') {
            echo "Error: " . $e->getMessage() . PHP_EOL;
            return;
        }

        echo "<h1>Error en el sistema</h1>";
        echo "<p>Ha ocurrido un error inesperado. Los detalles se han registrado en los logs.</p>";
    }

    public function handleError(
        int $errno,
        string $errstr,
        ?string $errfile = null,
        ?int $errline = null
    ): bool {
        $this->handleException(new \ErrorException(
            $errstr,
            0,
            $errno,
            $errfile ?? '',
            $errline ?? 0
        ));
        return true;
    }
}
