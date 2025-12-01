<?php
// public/reports.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';

require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';

require_once BASE_PATH . '/src/Services/AuthorizationService.php';
require_once BASE_PATH . '/src/Services/ReportService.php';
require_once BASE_PATH . '/src/Controllers/ReportController.php';

use App\Security\SessionManager;
use App\Controllers\ReportController;

SessionManager::requireLogin();

$controller = new ReportController();

$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    case 'income':
        $controller->incomeStatement();
        break;

    case 'balance':
        $controller->balanceSheet();
        break;

    case 'sign':
        if ($id === null) {
            header('Location: reports.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->signReport($id);
        } else {
            $controller->showSignForm($id);
        }
        break;

    default: // index / lista
        $controller->index();
}
