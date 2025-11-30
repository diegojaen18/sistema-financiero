<?php
// public/logout.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

// Misma lista de requires que en login.php, para que carguen TODAS las clases necesarias
require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';

require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';

require_once BASE_PATH . '/src/Repositories/UserRepository.php';

require_once BASE_PATH . '/src/Services/AuthService.php';

require_once BASE_PATH . '/src/Controllers/AuthController.php';

use App\Controllers\AuthController;

$controller = new AuthController();
$controller->logout();
