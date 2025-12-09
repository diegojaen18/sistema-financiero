<?php
// public/reports.php

require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/src/Database/Connection.php';

require_once BASE_PATH . '/src/Interfaces/ValidatorInterface.php';

require_once BASE_PATH . '/src/Security/Validator.php';
require_once BASE_PATH . '/src/Security/Sanitizer.php';
require_once BASE_PATH . '/src/Security/SessionManager.php';
require_once BASE_PATH . '/config/security.php';

require_once BASE_PATH . '/src/Services/AuthorizationService.php';
require_once BASE_PATH . '/src/Services/ReportService.php';
require_once BASE_PATH . '/src/Controllers/ReportController.php';

use App\Security\SessionManager;
use App\Controllers\ReportController;
use App\Services\AuthorizationService;

SessionManager::requireLogin();

$authService = new AuthorizationService();
$userId      = SessionManager::get('user_id');

$isAdmin   = $authService->userHasRoleName($userId, 'Administrador');
$isGerente = $authService->userHasRoleName($userId, 'Gerente Financiero');
$isAuditor = $authService->userHasRoleName($userId, 'Auditor');

$controller = new ReportController();

$action = $_GET['action'] ?? 'index';
$search = $_GET['search'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($action) {

    /* =========================================
     * GENERACIÓN DE INFORMES (solo Gerente)
     * ========================================= */
    case 'income':
        // Solo el Gerente Financiero puede generar Estado de Resultados
        if (!$isGerente) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }
        $controller->incomeStatement();
        break;

    case 'balance':
        // Solo el Gerente Financiero puede generar Balance General
        if (!$isGerente) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }
        $controller->balanceSheet();
        break;

    /* =========================================
     * FIRMA Y DESCARGA
     * ========================================= */

    case 'sign':
        if ($id <= 0) {
            header('Location: reports.php?msg=notfound');
            exit;
        }

        // La lógica de "solo Gerente firma" ya está en ReportController::signReport
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->signReport($id);
        } else {
            $controller->showSignForm($id);
        }
        break;

    case 'download':
        if ($id <= 0) {
            header('Location: reports.php?msg=notfound');
            exit;
        }
        // Dentro del controlador se valida Admin / Gerente / Auditor
        $controller->downloadSigned($id);
        break;

    /* =========================================
     * VALIDAR REPORTES (PDF + hash)
     * Admin + Gerente + Auditor
     * ========================================= */

    case 'validate':
        if (!($isAdmin || $isGerente || $isAuditor)) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }
        $controller->showValidateForm();
        break;

    case 'validate_post':
        if (!($isAdmin || $isGerente || $isAuditor)) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->validateUploadedReport();
        } else {
            header('Location: reports.php?action=validate');
        }
        break;

    /* =========================================
     * LISTA / HISTORIAL (INDEX)
     * ========================================= */

    default:
        $controller->index($search);
        break;
}
