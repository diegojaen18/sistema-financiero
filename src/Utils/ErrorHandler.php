<?php
namespace SistemaFinanciero\Utils;

use SistemaFinanciero\Interfaces\ErrorHandlerInterface;

class ErrorHandler implements ErrorHandlerInterface {
    
    private array $errors = [];
    
    public function handle(\Throwable $error): void {
        $this->log($error->getMessage(), [
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString()
        ]);
        
        if (APP_ENV === 'development') {
            echo "<div style='background:#f8d7da;padding:20px;border:2px solid #dc3545;margin:20px;'>";
            echo "<h3>Error:</h3>";
            echo "<p><strong>" . htmlspecialchars($error->getMessage()) . "</strong></p>";
            echo "<p>File: " . $error->getFile() . " (Line: " . $error->getLine() . ")</p>";
            echo "</div>";
        } else {
            echo "<p>Ha ocurrido un error. Por favor contacte al administrador.</p>";
        }
    }
    
    public function log(string $message, array $context = []): void {
        $this->errors[] = [
            'message' => $message,
            'context' => $context,
            'time' => date('Y-m-d H:i:s')
        ];
        
        $logFile = LOGS_PATH . '/error-' . date('Y-m-d') . '.log';
        $logMessage = sprintf(
            "[%s] %s %s\n",
            date('Y-m-d H:i:s'),
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        
        @file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}