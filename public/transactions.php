<?php
// public/transactions.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';

require_once BASE_PATH . '/src/Repositories/AccountRepository.php';
require_once BASE_PATH . '/src/Repositories/TransactionRepository.php';
require_once BASE_PATH . '/src/Controllers/TransactionController.php';

use App\Security\SessionManager;
use App\Controllers\TransactionController;

SessionManager::requireLogin();

$controller = new TransactionController();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleCreate();
        } else {
            $controller->showCreate();
        }
        break;

    case 'view':
        if ($id !== null) {
            $controller->view($id);
        } else {
            header('Location: transactions.php');
        }
        break;

    case 'delete':
        if ($id !== null) {
            $controller->delete($id);
        } else {
            header('Location: transactions.php');
        }
        break;

    default:
        $controller->listTransactions();
}
