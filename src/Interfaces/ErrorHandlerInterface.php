<?php
// src/Interfaces/ErrorHandlerInterface.php

namespace App\Interfaces;

interface ErrorHandlerInterface
{
    public function handleException(\Throwable $e): void;

    public function handleError(
        int $errno,
        string $errstr,
        ?string $errfile = null,
        ?int $errline = null
    ): bool;
}
