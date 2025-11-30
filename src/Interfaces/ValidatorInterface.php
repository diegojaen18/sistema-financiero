<?php
namespace src\Security;

use SistemaFinanciero\Interfaces\ValidatorInterface;

class Validator implements ValidatorInterface {
    private array $errors = [];

    public function validate(array $data, array $rules): bool {
        $this->clearErrors();

        foreach ($rules as $field => $ruleList) {
            foreach ($ruleList as $rule) {

                if ($rule === 'required' && empty($data[$field])) {
                    $this->errors[$field][] = "$field is required";
                }

                if ($rule === 'email' && !filter_var($data[$field] ?? '', FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "$field must be a valid email";
                }

                if ($rule === 'min:6' && isset($data[$field]) && strlen($data[$field]) < 6) {
                    $this->errors[$field][] = "$field must have at least 6 characters";
                }

            }
        }

        return !$this->hasErrors();
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    public function clearErrors(): void {
        $this->errors = [];
    }
}
