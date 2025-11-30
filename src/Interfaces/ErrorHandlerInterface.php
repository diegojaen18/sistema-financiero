<?php
/**
 * ErrorHandlerInterface
 * Sistema Financiero - UTP
 */

namespace SistemaFinanciero\Interfaces;

interface ErrorHandlerInterface {
    
    public function handle(\Throwable $error): void;
    
    public function log(string $message, array $context = []): void;
    
    public function getErrors(): array;
}