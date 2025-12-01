<?php
// src/Utils/Logger.php

namespace App\Utils;

class Logger
{
    private string $logFile;

    public function __construct(?string $filePath = null)
    {
        // /src/Utils -> /src -> / (proyecto)
        $this->logFile = $filePath ?: (dirname(__DIR__, 2) . '/storage/logs/app.log');
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $line = sprintf(
            "[%s] [%s] %s %s\n",
            $date,
            $level,
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );

        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        @file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
