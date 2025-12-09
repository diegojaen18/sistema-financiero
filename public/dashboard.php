<?php
// public/dashboard.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/src/Services/AuthorizationService.php';

use App\Security\SessionManager;
use App\Services\AuthorizationService;

SessionManager::requireLogin();

$full_name = SessionManager::get('full_name');
$pageTitle = 'Dashboard - ' . APP_NAME;

$authService = new AuthorizationService();
$userId      = SessionManager::get('user_id');

$isContador = $authService->userHasRoleName($userId, 'Contador');
$isAuditor  = $authService->userHasRoleName($userId, 'Auditor');
$isGerente  = $authService->userHasRoleName($userId, 'Gerente Financiero');
$isAdmin    = $authService->userHasRoleName($userId, 'Administrador');

include BASE_PATH . '/views/layouts/header.php';
?>

<section class="dashboard-hero">
    <div>
        <h1>Panel principal</h1>
        <p>Bienvenido, <?= htmlspecialchars($full_name) ?>.</p>
        <p class="dashboard-subtitle">
            Desde aquí puedes acceder a los módulos del sistema según tus permisos.
        </p>

        <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'noperm'): ?>
            <div class="alert alert-error mt-2">
                No tienes permisos para acceder a la sección solicitada.
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="dashboard-grid">
    <?php if (!$isContador && !$isAuditor && !$isGerente): ?>
        <!-- Usuarios: NO visible para Contador, Auditor ni Gerente -->
        <a href="<?= BASE_URL ?>/users.php" class="dashboard-card">
            <div class="dashboard-card-icon">👥</div>
            <h2>Usuarios</h2>
            <p>Administra los usuarios del sistema y sus accesos.</p>
        </a>
    <?php endif; ?>

    <?php if (!$isAuditor): ?>
        <!-- Cuentas: visible para todos menos Auditor -->
        <a href="<?= BASE_URL ?>/accounts.php" class="dashboard-card">
            <div class="dashboard-card-icon">📚</div>
            <h2>Catálogo de Cuentas</h2>
            <p>Consulta las cuentas contables utilizadas en el sistema.</p>
        </a>
    <?php endif; ?>

    <?php if (!$isAuditor): ?>
        <!-- Transacciones: visible para todos menos Auditor -->
        <a href="<?= BASE_URL ?>/transactions.php" class="dashboard-card">
            <div class="dashboard-card-icon">🧾</div>
            <h2>Transacciones</h2>
            <p>Registra y consulta las operaciones del Diario General.</p>
        </a>
    <?php endif; ?>

    <?php if (!$isContador): ?>
        <!-- Informes: todos menos el Contador -->
        <a href="<?= BASE_URL ?>/reports.php" class="dashboard-card">
            <div class="dashboard-card-icon">📊</div>
            <h2>Informes Financieros</h2>
            <p>Genera y revisa estados financieros del sistema.</p>
        </a>
    <?php endif; ?>

    <?php if ($isAuditor || $isGerente || $isAdmin): ?>
        <!-- Validar reportes: Auditor, Gerente, Admin -->
        <a href="<?= BASE_URL ?>/reports.php?action=validate" class="dashboard-card">
            <div class="dashboard-card-icon">✔️</div>
            <h2>Validar reportes</h2>
            <p>Verifica la integridad de reportes firmados con hash.</p>
        </a>
    <?php endif; ?>

    <?php if ($isAuditor): ?>
        <!-- Logs: solo Auditor -->
        <a href="<?= BASE_URL ?>/logs.php" class="dashboard-card">
            <div class="dashboard-card-icon">🗂️</div>
            <h2>Logs del sistema</h2>
            <p>Consulta de auditoría sobre operaciones del sistema.</p>
        </a>
    <?php endif; ?>
</section>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
