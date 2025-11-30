<?php
namespace src\Security;

use src\Interfaces\ValidatorInterface;

class Sanitizer {
    public function cleanString(string $input): string {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
}
