<?php
// views/layouts/header.php

use App\Security\SessionManager;

SessionManager::start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="dashboard-body">
<?php include BASE_PATH . '/views/layouts/nav.php'; ?>
<main class="dashboard-main">
