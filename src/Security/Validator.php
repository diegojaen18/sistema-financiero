<?php
// src/Security/Validator.php

namespace App\Security;

use App\Interfaces\ValidatorInterface;

class Validator implements ValidatorInterface
{
    private array $errors = [];

    public function validateRequired(array $data, array $fields): array
    {
        $this->errors = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[$field] = "El campo {$field} es obligatorio.";
            }
        }

        return $this->errors;
    }

    public function validateEmail(string $email, string $fieldName = 'email'): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$fieldName] = 'El correo electrónico no es válido.';
        }

        return $this->errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
