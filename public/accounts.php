<?php
// public/accounts.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';

require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/src/Repositories/AccountRepository.php';
require_once BASE_PATH . '/src/Controllers/AccountController.php';

use App\Security\SessionManager;
use App\Controllers\AccountController;

SessionManager::requireLogin();

$controller = new AccountController();

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
            header('Location: accounts.php');
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
            header('Location: accounts.php');
        }
        break;

    default:
        $controller->listAccounts();
}
