<?php
namespace src\Controllers;

use src\Services\AuthService;

class AuthController {
    private AuthService $auth;

    public function __construct() {
        session_start();
        $this->auth = new AuthService();
    }

    public function attemptLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->auth->login($_POST['username'], $_POST['password']);

            if ($result['success']) {
                header("Location: dashboard.php");
                exit();
            }

            $_SESSION['login_error'] = $result['errors'][0] ?? $result['message'];
            header("Location: login.php");
            exit();
        }
    }
}
