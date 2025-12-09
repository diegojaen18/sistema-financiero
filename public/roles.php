<?php
// public/roles.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';
require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';

require_once BASE_PATH . '/src/Repositories/UserRepository.php';
require_once BASE_PATH . '/src/Repositories/RoleRepository.php';
require_once BASE_PATH . '/src/Services/AuthorizationService.php';
require_once BASE_PATH . '/src/Controllers/RoleController.php';

use App\Security\SessionManager;
use App\Services\AuthorizationService;
use App\Controllers\RoleController;

SessionManager::requireLogin();

// Solo el Administrador puede entrar a roles.php
$authService = new AuthorizationService();
$userId      = SessionManager::get('user_id');
$isAdmin     = $authService->userHasRoleName($userId, 'Administrador');

if (!$isAdmin) {
    header('Location: dashboard.php?msg=noperm');
    exit;
}

$controller = new RoleController();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$search = $_GET['search'] ?? '';

switch ($action) {
    case 'edit':
        if ($id <= 0) {
            header('Location: roles.php?msg=notfound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateUserRoles($id);
        } else {
            $controller->editUserRoles($id);
        }
        break;

    default: // list
        $controller->listUsers($search);
        break;
}
