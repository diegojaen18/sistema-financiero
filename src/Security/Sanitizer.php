<?php
// src/Security/Sanitizer.php

namespace App\Security;

class Sanitizer
{
    public static function cleanString(?string $value): string
    {
        return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
    }

    public static function cleanArray(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[$key] = is_string($value)
                ? self::cleanString($value)
                : $value;
        }
        return $clean;
    }
}
