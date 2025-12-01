<?php
// config/security.php

use App\Utils\ErrorHandler;

require_once BASE_PATH . '/src/Interfaces/ErrorHandlerInterface.php';
require_once BASE_PATH . '/src/Utils/Logger.php';
require_once BASE_PATH . '/src/Utils/ErrorHandler.php';

// Registrar manejadores globales de errores y excepciones
$__appErrorHandler = new ErrorHandler();
set_error_handler([$__appErrorHandler, 'handleError']);
set_exception_handler([$__appErrorHandler, 'handleException']);

// Cabeceras básicas de seguridad
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
