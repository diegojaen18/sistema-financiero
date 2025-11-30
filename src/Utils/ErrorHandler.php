<?php
namespace SistemaFinanciero\Utils;

use SistemaFinanciero\Interfaces\ErrorHandlerInterface;

class ErrorHandler implements ErrorHandlerInterface {
    public static function handle(\Throwable $error, string $context = ""): void {
        $msg = date("[Y-m-d H:i:s]") . " [$context] " . $error->getMessage() . PHP_EOL;
        file_put_contents(__DIR__ . "/../../storage/logs/error.log", $msg, FILE_APPEND);
    }
}
