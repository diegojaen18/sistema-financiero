<?php
// public/users.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';

require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/src/Repositories/UserRepository.php';
require_once BASE_PATH . '/src/Controllers/UserController.php';
require_once BASE_PATH . '/src/Services/AuthorizationService.php';

use App\Security\SessionManager;
use App\Controllers\UserController;
use App\Services\AuthorizationService;

SessionManager::requireLogin();

$authService = new AuthorizationService();
$userId      = SessionManager::get('user_id');
$isGerente   = $authService->userHasRoleName($userId, 'Gerente Financiero');

// Gerente Financiero: no puede entrar a usuarios
if ($isGerente) {
    header('Location: dashboard.php?msg=noperm');
    exit;
}

$controller = new UserController();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleCreate();
        } else {
            $controller->showCreate();
        }
        break;

    case 'edit':
        if ($id === null) {
            header('Location: users.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleEdit($id);
        } else {
            $controller->showEdit($id);
        }
        break;

    case 'toggle':
        if ($id !== null) {
            $controller->toggleStatus($id);
        } else {
            header('Location: users.php');
        }
        break;

    default:
        $search = $_GET['search'] ?? '';
        $controller->listUsers($search);
}
