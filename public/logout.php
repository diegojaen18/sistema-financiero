<?php
/**
 * Logout - Cerrar Sesión
 * Sistema Financiero - UTP
 */

require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../src/Security/SessionManager.php';

use SistemaFinanciero\Security\SessionManager;

// Destruir sesión
SessionManager::destroy();

// Redirigir a login con mensaje
header('Location: login.php');
exit;