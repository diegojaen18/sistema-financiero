<?php
// src/Interfaces/ValidatorInterface.php

namespace App\Interfaces;

interface ValidatorInterface
{
    public function validateRequired(array $data, array $fields): array;
    public function validateEmail(string $email, string $fieldName = 'email'): array;
}
