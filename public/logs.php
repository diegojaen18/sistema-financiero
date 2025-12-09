<?php
// public/logs.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/src/Services/AuthorizationService.php';
require_once BASE_PATH . '/src/Repositories/TransactionRepository.php';

use App\Security\SessionManager;
use App\Services\AuthorizationService;
use App\Repositories\TransactionRepository;

SessionManager::requireLogin();

$userId      = SessionManager::get('user_id');
$authService = new AuthorizationService();
$isAuditor   = $authService->userHasRoleName($userId, 'Auditor');

// Solo el Auditor puede entrar a logs
if (!$isAuditor) {
    header('Location: dashboard.php?msg=noperm');
    exit;
}

$repo   = new TransactionRepository();
$search = $_GET['search'] ?? '';
$search = trim($search);

if ($search === '') {
    $logs = $repo->findAll();
} else {
    $logs = $repo->search($search);
}

$pageTitle     = 'Logs del sistema - ' . APP_NAME;
$currentSearch = $search;

include BASE_PATH . '/views/logs/list.php';
