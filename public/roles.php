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
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/src/Repositories/UserRepository.php';
require_once BASE_PATH . '/src/Repositories/RoleRepository.php';
require_once BASE_PATH . '/src/Services/AuthorizationService.php';
require_once BASE_PATH . '/src/Controllers/RoleController.php';

use App\Security\SessionManager;
use App\Controllers\RoleController;

SessionManager::requireLogin();

$controller = new RoleController();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($id === null) {
                header('Location: roles.php');
                exit;
            }
            $controller->updateUserRoles($id);
        } else {
            if ($id === null) {
                header('Location: roles.php');
                exit;
            }
            $controller->editUserRoles($id);
        }
        break;

    default:
        $controller->listUsers();
}
