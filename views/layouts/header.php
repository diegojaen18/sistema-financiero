<?php
/**
 * Header Layout
 * Sistema Financiero - UTP
 */

use SistemaFinanciero\Security\SessionManager;

$currentUser = SessionManager::getFullName();
$currentUsername = SessionManager::getUsername();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistema Financiero' ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
    <div class="header">
        <div class="header-content">
            <h1>💼 <?= APP_NAME ?></h1>
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($currentUser) ?></span>
                <span>(<?= htmlspecialchars($currentUsername) ?>)</span>
                <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </div>