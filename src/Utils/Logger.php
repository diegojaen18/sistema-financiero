<?php
namespace SistemaFinanciero\Utils;

class Logger {
    
    public static function info(string $message, array $context = []): void {
        self::write('INFO', $message, $context);
    }
    
    public static function error(string $message, array $context = []): void {
        self::write('ERROR', $message, $context);
    }
    
    public static function warning(string $message, array $context = []): void {
        self::write('WARNING', $message, $context);
    }
    
    public static function debug(string $message, array $context = []): void {
        if (APP_ENV === 'development') {
            self::write('DEBUG', $message, $context);
        }
    }
    
    private static function write(string $level, string $message, array $context): void {
        $logFile = LOGS_PATH . '/app-' . date('Y-m-d') . '.log';
        
        $logEntry = sprintf(
            "[%s] [%s] %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}