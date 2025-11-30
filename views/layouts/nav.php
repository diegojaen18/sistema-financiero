<?php
/**
 * Navigation Layout
 * Sistema Financiero - UTP
 */

// Detectar página actual
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="nav">
    <ul class="nav-list">
        <li>
            <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                🏠 Dashboard
            </a>
        </li>
        <li>
            <a href="users.php" class="<?= $currentPage === 'users.php' ? 'active' : '' ?>">
                👥 Usuarios
            </a>
        </li>
        <li>
            <a href="accounts.php" class="<?= $currentPage === 'accounts.php' ? 'active' : '' ?>">
                📊 Catálogo de Cuentas
            </a>
        </li>
        <li>
            <a href="transactions.php" class="<?= $currentPage === 'transactions.php' ? 'active' : '' ?>">
                💰 Transacciones
            </a>
        </li>
        <li>
            <a href="reports.php" class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                📈 Reportes
            </a>
        </li>
    </ul>
</nav>