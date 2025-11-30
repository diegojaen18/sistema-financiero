<?php
namespace src\Services;

use src\Database\Connection;
use src\Security\Sanitizer;
use src\Security\Validator;
use PDO;

class AuthService {
    private PDO $pdo;
    private Sanitizer $sanitizer;
    private Validator $validator;

    public function __construct() {
        $this->pdo = Connection::getInstance()->getPDO();
        $this->sanitizer = new Sanitizer();
        $this->validator = new Validator();
    }

    public function login(string $username, string $password): array {
        $input = [
            'username' => $username,
            'password' => $password
        ];

        $errors = $this->validator->validate($input);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $username = $this->sanitizer->cleanString($username);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !$user['is_active']) {
            return ['success' => false, 'message' => "Invalid credentials"];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => "Invalid credentials"];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        $update = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->execute([$user['id']]);

        return ['success' => true, 'user' => $user];
    }
}
