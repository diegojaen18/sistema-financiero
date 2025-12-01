<?php
// public/index.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';

require_once BASE_PATH . '/src/Security/SessionManager.php';

use App\Security\SessionManager;

$pageTitle = 'Sistema Financiero - Página pública';

include BASE_PATH . '/views/layouts/header.php';
?>

<section class="dashboard-hero">
    <div>
        <h1>Sistema Financiero Contable</h1>
        <p>
            Este sistema permite registrar, controlar y analizar las transacciones 
            financieras de una organización de forma estructurada.
        </p>
        <p class="dashboard-subtitle">
            Al registrar todas las operaciones en un Diario General y generar informes
            como el Estado de Resultados y el Balance General, es posible tomar mejores
            decisiones, cumplir con obligaciones legales y mantener la información 
            financiera confiable.
        </p>
        <div class="mt-2">
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-primary">Iniciar sesión</a>
        </div>
        <p class="small-note mt-2">
            Solo usuarios autorizados pueden acceder al panel. Si no tienes acceso,
            contacta al administrador del sistema.
        </p>
    </div>
</section>

<section class="dashboard-grid">
    <div class="dashboard-card">
        <div class="dashboard-card-icon">🧾</div>
        <h2>Registro estructurado</h2>
        <p>Las transacciones se almacenan en un Diario General con partida doble, garantizando la consistencia contable.</p>
    </div>
    <div class="dashboard-card">
        <div class="dashboard-card-icon">📚</div>
        <h2>Catálogo de cuentas</h2>
        <p>Las cuentas se clasifican por Activos, Pasivos, Patrimonio, Ingresos y Gastos según la estructura contable.</p>
    </div>
    <div class="dashboard-card">
        <div class="dashboard-card-icon">📊</div>
        <h2>Informes financieros</h2>
        <p>El sistema genera automáticamente Estado de Resultados y Balance General a partir de los registros.</p>
    </div>
    <div class="dashboard-card">
        <div class="dashboard-card-icon">✅</div>
        <h2>Control y trazabilidad</h2>
        <p>Cada operación registra el usuario que la creó, los roles asignados y las firmas de los informes.</p>
    </div>
</section>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
