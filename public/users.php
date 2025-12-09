<?php
// public/users.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';

require_once BASE_PATH . '/src/Repositories/UserRepository.php';
require_once BASE_PATH . '/src/Services/AuthorizationService.php';
require_once BASE_PATH . '/src/Controllers/UserController.php';

use App\Security\SessionManager;
use App\Services\AuthorizationService;
use App\Controllers\UserController;

SessionManager::requireLogin();

// Solo el Administrador puede gestionar usuarios
$authService = new AuthorizationService();
$userId      = SessionManager::get('user_id');
$isAdmin     = $authService->userHasRoleName($userId, 'Administrador');

if (!$isAdmin) {
    header('Location: dashboard.php?msg=noperm');
    exit;
}

$controller = new UserController();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleCreate();
        } else {
            $controller->showCreate();
        }
        break;

    case 'edit':
        if ($id <= 0) {
            header('Location: users.php?msg=notfound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleEdit($id);
        } else {
            $controller->showEdit($id);
        }
        break;

    case 'toggle':
        if ($id <= 0) {
            header('Location: users.php?msg=notfound');
            exit;
        }
        $controller->toggleStatus($id);
        break;

    default: // list
        $search = $_GET['search'] ?? '';
        $controller->listUsers($search);
        break;
}
