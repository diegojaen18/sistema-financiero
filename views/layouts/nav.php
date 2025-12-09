<?php
// views/layouts/nav.php

use App\Security\SessionManager;
use App\Services\AuthorizationService;

require_once BASE_PATH . '/src/Services/AuthorizationService.php';

$userId    = SessionManager::get('user_id');
$full_name = SessionManager::get('full_name');

$isAdmin     = false;
$isContador  = false;
$isAuditor   = false;
$isGerente   = false; // Gerente Financiero

if ($userId) {
    $authService = new AuthorizationService();
    $isAdmin     = $authService->userHasRoleName($userId, 'Administrador');
    $isContador  = $authService->userHasRoleName($userId, 'Contador');
    $isAuditor   = $authService->userHasRoleName($userId, 'Auditor');
    $isGerente   = $authService->userHasRoleName($userId, 'Gerente Financiero');
}
?>
<nav class="top-nav">
    <div class="nav-left">
        <span class="brand">
            <a href="<?= BASE_URL ?>/dashboard.php" class="nav-link" style="padding-left:0;">
                <?= htmlspecialchars(APP_NAME) ?>
            </a>
        </span>
    </div>

    <div class="nav-right">
        <?php if ($userId): ?>
            <?php if ($full_name): ?>
                <span class="user-info">Hola, <?= htmlspecialchars($full_name) ?></span>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/dashboard.php" class="nav-link">Dashboard</a>

            <?php if ($isAuditor): ?>
                <!-- AUDITOR: informes (solo lectura), validar reportes, logs -->
                <a href="<?= BASE_URL ?>/reports.php" class="nav-link">Informes</a>
                <a href="<?= BASE_URL ?>/reports.php?action=validate" class="nav-link">Validar reportes</a>
                <a href="<?= BASE_URL ?>/logs.php" class="nav-link">Logs</a>

            <?php elseif ($isContador): ?>
                <!-- CONTADOR: cuentas + transacciones -->
                <a href="<?= BASE_URL ?>/accounts.php" class="nav-link">Cuentas</a>
                <a href="<?= BASE_URL ?>/transactions.php" class="nav-link">Transacciones</a>

            <?php elseif ($isGerente): ?>
                <!-- GERENTE FINANCIERO:
                     - No ve Usuarios ni Roles
                     - Sí ve cuentas, transacciones, informes, validar reportes
                -->
                <a href="<?= BASE_URL ?>/accounts.php" class="nav-link">Cuentas</a>
                <a href="<?= BASE_URL ?>/transactions.php" class="nav-link">Transacciones</a>
                <a href="<?= BASE_URL ?>/reports.php" class="nav-link">Informes</a>
                <a href="<?= BASE_URL ?>/reports.php?action=validate" class="nav-link">Validar reportes</a>

            <?php else: ?>
                <!-- Otros roles (ej. Administrador) -->

                <?php if ($isAdmin): ?>
                    <a href="<?= BASE_URL ?>/users.php" class="nav-link">Usuarios</a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/accounts.php" class="nav-link">Cuentas</a>
                <a href="<?= BASE_URL ?>/transactions.php" class="nav-link">Transacciones</a>
                <a href="<?= BASE_URL ?>/reports.php" class="nav-link">Informes</a>

                <?php if ($isAdmin): ?>
                    <a href="<?= BASE_URL ?>/reports.php?action=validate" class="nav-link">Validar reportes</a>
                    <a href="<?= BASE_URL ?>/roles.php" class="nav-link">Roles</a>
                <?php endif; ?>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/logout.php" class="btn btn-secondary btn-small">Cerrar sesión</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-primary btn-small">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</nav>
