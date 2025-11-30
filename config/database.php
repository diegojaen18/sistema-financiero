<?php
/**
 * Configuración de Base de Datos
 * Sistema Financiero - UTP
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '8889'); // Cambiar a 3306 si usas otro puerto
define('DB_NAME', 'sistema_financiero');
define('DB_USER', 'root');
define('DB_PASS', 'root');

define('APP_ENV', 'development'); // development o production

date_default_timezone_set('America/Panama');

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}