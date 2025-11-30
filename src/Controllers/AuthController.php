<?php
// src/Controllers/AuthController.php

namespace App\Controllers;

use App\Services\AuthService;
use App\Security\SessionManager;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(?array $errors = null): void
    {
        $errors = $errors ?? [];
        // Variable disponible para la vista
        $pageTitle = 'Login - ' . APP_NAME;
        include BASE_PATH . '/views/auth/login.php';
    }

    public function handleLogin(): void
    {
        $result = $this->authService->login($_POST);

        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        }

        $this->showLogin($result['errors']);
    }

    public function logout(): void
    {
        $this->authService->logout();
        header('Location: login.php');
        exit;
    }
}
