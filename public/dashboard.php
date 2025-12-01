<?php
// public/dashboard.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';

use App\Security\SessionManager;

SessionManager::requireLogin();

$full_name = SessionManager::get('full_name');
$pageTitle = 'Dashboard - ' . APP_NAME;

include BASE_PATH . '/views/layouts/header.php';
?>

<section class="dashboard-hero">
    <div>
        <h1>Panel principal</h1>
        <p>Bienvenido, <?= htmlspecialchars($full_name) ?>.</p>
        <p class="dashboard-subtitle">
            Desde aquí puedes gestionar usuarios, cuentas contables, transacciones e informes financieros.
        </p>
    </div>
</section>

<section class="dashboard-grid">
    <a href="<?= BASE_URL ?>/users.php" class="dashboard-card">
        <div class="dashboard-card-icon">👥</div>
        <h2>Usuarios</h2>
        <p>Administra los usuarios del sistema y sus accesos.</p>
    </a>

    <a href="<?= BASE_URL ?>/accounts.php" class="dashboard-card">
        <div class="dashboard-card-icon">📚</div>
        <h2>Catálogo de Cuentas</h2>
        <p>Configura las cuentas contables utilizadas en el sistema.</p>
    </a>

    <a href="<?= BASE_URL ?>/transactions.php" class="dashboard-card">
        <div class="dashboard-card-icon">🧾</div>
        <h2>Transacciones</h2>
        <p>Registra las operaciones del Diario General con partida doble.</p>
    </a>

    <a href="<?= BASE_URL ?>/reports.php" class="dashboard-card">
        <div class="dashboard-card-icon">📊</div>
        <h2>Informes Financieros</h2>
        <p>Genera estado de resultados y balance general del período.</p>
    </a>
</section>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
