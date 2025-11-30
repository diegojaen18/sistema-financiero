<?php
/**
 * Constantes del Sistema
 * Sistema Financiero - UTP
 */

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');

// IMPORTANTE: Ajusta el puerto según tu MAMP
define('BASE_URL', 'http://localhost:8888/sistema-financiero/public');

define('APP_NAME', 'Sistema Financiero');
define('APP_VERSION', '1.0.0');

define('SESSION_TIMEOUT', 1800);
define('RECORDS_PER_PAGE', 20);

define('ROLE_ADMIN', 'Administrador');
define('ROLE_CONTADOR', 'Contador');
define('ROLE_GERENTE', 'Gerente Financiero');
define('ROLE_AUDITOR', 'Auditor');

// Cargar funciones helper
require_once ROOT_PATH . '/src/Utils/helpers.php';