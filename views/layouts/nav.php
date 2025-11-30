<?php
// views/layouts/nav.php

use App\Security\SessionManager;

$full_name = SessionManager::get('full_name');
?>
<nav class="top-nav">
    <div>
        <span class="brand"><?= APP_NAME ?></span>
    </div>
    <div class="nav-right">
        <?php if ($full_name): ?>
            <span class="user-info">Hola, <?= htmlspecialchars($full_name) ?></span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/dashboard.php" class="nav-link">Dashboard</a>
        <a href="<?= BASE_URL ?>/users.php" class="nav-link">Usuarios</a>
        <a href="<?= BASE_URL ?>/accounts.php" class="nav-link">Cuentas</a>
        <!-- luego: Transacciones, Reportes, etc. -->
        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>
</nav>
