<?php
// public/dashboard.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Security/SessionManager.php';

use App\Security\SessionManager;

SessionManager::requireLogin();

$full_name = SessionManager::get('full_name');
$pageTitle = 'Dashboard - ' . APP_NAME;

require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/views/layouts/header.php';
?>

<h1>Panel principal</h1>
<p>Bienvenido, <?= htmlspecialchars($full_name) ?>.</p>

<ul>
    <li><a href="<?= BASE_URL ?>/users.php">Módulo de Usuarios</a></li>
    <li><a href="<?= BASE_URL ?>/accounts.php">Catálogo de Cuentas</a></li>
    <li><a href="<?= BASE_URL ?>/transactions.php">Transacciones (Diario General)</a></li>
    <li><a href="<?= BASE_URL ?>/reports.php">Informes Financieros</a></li>
</ul>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
