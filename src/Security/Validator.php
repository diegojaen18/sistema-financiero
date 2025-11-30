<?php
namespace src\Security;

use src\Interfaces\ValidatorInterface.php;

class Validator implements ValidatorInterface {
    public function validate(array $data): array {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = "Username is required";
        }

        if (empty($data['password'])) {
            $errors[] = "Password is required";
        }

        return $errors;
    }
}
