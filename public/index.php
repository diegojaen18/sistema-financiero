<?php
// public/index.php
require_once __DIR__ . '/../config/constants.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="public-body">
    <header class="public-header">
        <h1><?= APP_NAME ?></h1>
        <p>Sistema para registrar y controlar las transacciones financieras de una empresa.</p>
        <a href="login.php" class="btn btn-primary">Entrar al sistema</a>
    </header>

    <section class="public-section">
        <h2>¿Por qué es importante registrar nuestras transacciones?</h2>
        <p>
            Un sistema financiero nos permite llevar un control exacto de ingresos, gastos,
            activos y pasivos. Esto facilita la toma de decisiones, el cumplimiento de obligaciones
            fiscales y la generación de informes confiables para gerentes, contadores y auditores.
        </p>
        <p>
            Además, al contar con un catálogo de cuentas bien estructurado y un diario general,
            se puede garantizar que cada movimiento quede respaldado y sea trazable.
        </p>
    </section>
</body>
</html>
