<?php
namespace SistemaFinanciero\Interfaces;

interface ErrorHandlerInterface {
    public static function handle(\Throwable $error, string $context = ""): void;
}
