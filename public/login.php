<?php
// public/login.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';
// Autoload sencillo con requires (sin Composer)
require_once BASE_PATH . '/src/Database/Connection.php';
require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';
require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/src/Repositories/UserRepository.php';
require_once BASE_PATH . '/src/Services/AuthService.php';
require_once BASE_PATH . '/src/Controllers/AuthController.php';

// Usos de namespaces
use App\Controllers\AuthController;
use App\Security\SessionManager;

// Iniciar sesión (por si ya está logueado)
SessionManager::start();
if (SessionManager::has('user_id')) {
    header('Location: dashboard.php');
    exit;
}

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handleLogin();
} else {
    $controller->showLogin();
}
