<?php
// src/Controllers/ReportController.php

namespace App\Controllers;

use App\Services\ReportService;
use App\Services\AuthorizationService;
use App\Security\Validator;
use App\Security\Sanitizer;
use App\Security\SessionManager;

class ReportController
{
    private ReportService $reportService;
    private AuthorizationService $authService;
    private Validator $validator;

    public function __construct()
    {
        $this->reportService = new ReportService();
        $this->authService   = new AuthorizationService();
        $this->validator     = new Validator();
    }

    /**
     * Devuelve true si el usuario tiene rol "Administrador" y por tanto
     * NO debe poder generar nuevos informes.
     */
    private function isAdminLimited(): bool
    {
        $userId = SessionManager::get('user_id');
        return $this->authService->userHasRoleName($userId, 'Administrador');
    }

    public function index(string $search = ''): void
    {
        $userId   = SessionManager::get('user_id');
        $reports  = $this->reportService->listReports();
        $canSign  = $this->authService->userHasRoleName($userId, 'Gerente Financiero');
        $isAuditor = $this->authService->userHasRoleName($userId, 'Auditor');

        $search = trim($search);

        if ($search !== '') {
            $searchLower = mb_strtolower($search, 'UTF-8');

            $reports = array_filter($reports, function ($r) use ($searchLower) {
                $typeCode = $r['report_type'] ?? '';

                // Etiqueta legible del tipo
                $typeLabel = ($typeCode === 'income_statement')
                    ? 'estado de resultados'
                    : 'balance general';

                $periodStart = $r['period_start'] ?? '';
                $periodEnd   = $r['period_end']   ?? '';

                // Período tal como lo muestras en la tabla
                if ($periodStart === $periodEnd || empty($periodEnd)) {
                    $periodDisplay = $periodStart;
                } else {
                    $periodDisplay = $periodStart . ' al ' . $periodEnd;
                }

                $generatedBy = $r['generated_by_username'] ?? '';

                // Texto combinado donde vamos a buscar
                $joined = $typeCode . ' ' .
                        $typeLabel . ' ' .
                        $periodStart . ' ' .
                        $periodEnd . ' ' .
                        $periodDisplay . ' ' .
                        $generatedBy;

                $joinedLower = mb_strtolower($joined, 'UTF-8');

                return strpos($joinedLower, $searchLower) !== false;
            });
        }

        $pageTitle     = 'Informes Financieros - ' . APP_NAME;
        $currentSearch = $search;

        // Estas variables las usa la vista list.php
        include BASE_PATH . '/views/reports/list.php';
    }



    public function incomeStatement(): void
    {
        $pageTitle       = 'Estado de Resultados - ' . APP_NAME;
        $errors          = [];
        $reportData      = null;
        $reportGenerated = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = Sanitizer::cleanArray($_POST);
            $errors = $this->validator->validateRequired($data, ['period_start', 'period_end']);

            $periodStart = $data['period_start'] ?? '';
            $periodEnd   = $data['period_end'] ?? '';

            if ($periodStart && $periodEnd && $periodStart > $periodEnd) {
                $errors['period_end'] = 'La fecha fin debe ser mayor o igual que la fecha inicio.';
            }

            if (empty($errors)) {
                $userId = SessionManager::get('user_id');
                $result = $this->reportService->generateIncomeStatement($periodStart, $periodEnd, $userId);
                $reportData      = $result['data'];
                // Flag para mostrar popup en la vista
                $reportGenerated = true;
            }
        }

        include BASE_PATH . '/views/reports/income_statement.php';
    }


    public function balanceSheet(): void
    {
        $pageTitle       = 'Balance General - ' . APP_NAME;
        $errors          = [];
        $reportData      = null;
        $reportGenerated = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = Sanitizer::cleanArray($_POST);
            $errors = $this->validator->validateRequired($data, ['as_of_date']);

            $asOfDate = $data['as_of_date'] ?? '';

            if (empty($errors)) {
                $userId = SessionManager::get('user_id');
                $result = $this->reportService->generateBalanceSheet($asOfDate, $userId);
                $reportData      = $result['data'];
                // Flag para mostrar popup en la vista
                $reportGenerated = true;
            }
        }

        include BASE_PATH . '/views/reports/balance_sheet.php';
    }


    public function showSignForm(int $id): void
    {
        $userId = SessionManager::get('user_id');
        $report = $this->reportService->getReportById($id);

        if (!$report) {
            header('Location: reports.php?msg=notfound');
            exit;
        }

        $canSign = $this->authService->userHasRoleName($userId, 'Gerente Financiero');
        $error   = null;

        if (!$canSign) {
            $error = 'No tiene permisos para firmar este informe. Se requiere el rol de Gerente Financiero.';
        }

        $pageTitle = 'Firmar Informe - ' . APP_NAME;
        include BASE_PATH . '/views/reports/sign.php';
    }

    public function signReport(int $id): void
    {
        $userId  = SessionManager::get('user_id');
        $canSign = $this->authService->userHasRoleName($userId, 'Gerente Financiero');

        if (!$canSign) {
            header('Location: reports.php?msg=noperm');
            exit;
        }

        $hash = null;
        $ok   = $this->reportService->signReport($id, $userId, $hash);

        if ($ok) {
            // Pasamos hash y también el id del reporte para la descarga automática
            $extra = $hash ? '&hash=' . urlencode($hash) : '';
            header('Location: reports.php?msg=signed&id=' . $id . $extra);
        } else {
            header('Location: reports.php?msg=signerror');
        }
        exit;
    }






    public function downloadSigned(int $id): void
    {
        $userId = SessionManager::get('user_id');

        // Admin, Gerente Financiero y Auditor pueden descargar PDFs firmados
        $canDownload =
            $this->authService->userHasRoleName($userId, 'Gerente Financiero') ||
            $this->authService->userHasRoleName($userId, 'Administrador')     ||
            $this->authService->userHasRoleName($userId, 'Auditor');

        if (!$canDownload) {
            header('Location: reports.php?msg=noperm');
            exit;
        }

        // Usamos el PDF canónico generado por el servicio
        $pdfBinary = $this->reportService->getReportPdfBinary($id);
        if ($pdfBinary === null) {
            // No existe el reporte o no está firmado
            header('Location: reports.php?msg=notfound');
            exit;
        }

        $fileName = 'reporte_firmado_' . $id . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($pdfBinary));

        echo $pdfBinary;
        exit;
    }







        /**
     * Formulario para validar reportes mediante PDF + hash.
     * Visible para Admin, Gerente Financiero y Auditor.
     */
    public function showValidateForm(): void
    {
        $userId = SessionManager::get('user_id');

        $canValidate =
            $this->authService->userHasRoleName($userId, 'Administrador') ||
            $this->authService->userHasRoleName($userId, 'Gerente Financiero') ||
            $this->authService->userHasRoleName($userId, 'Auditor');

        if (!$canValidate) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }

        // Obtenemos listado de reportes para elegir cuál validar
        $reports   = $this->reportService->listReports();
        $errors    = [];
        $resultMsg = null;

        $pageTitle = 'Validar Reporte - ' . APP_NAME;

        include BASE_PATH . '/views/reports/validate.php';
    }

    /**
     * Procesa la validación de un reporte: compara el hash del PDF subido
     * con el hash almacenado en base de datos.
     *
     * NOTA: este método asume que en la tabla de reportes tienes columnas
     *       'pdf_hash' y 'hash_algo' (ej: sha256).
     */
    public function validateUploadedReport(): void
    {
        $userId = SessionManager::get('user_id');

        $canValidate =
            $this->authService->userHasRoleName($userId, 'Administrador') ||
            $this->authService->userHasRoleName($userId, 'Gerente Financiero') ||
            $this->authService->userHasRoleName($userId, 'Auditor');

        if (!$canValidate) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }

        $errors    = [];
        $resultMsg = null;
        $isValid   = null; // true / false cuando se compare el hash

        // 1) Validar que venga el id del reporte
        $reportId = isset($_POST['report_id']) ? (int)$_POST['report_id'] : 0;
        if ($reportId <= 0) {
            $errors['report_id'] = 'Debe seleccionar un reporte para validar.';
        }

        // 2) Validar archivo subido
        if (empty($_FILES['report_file']['name'])) {
            $errors['report_file'] = 'Debe adjuntar el archivo PDF del reporte.';
        } else {
            if ($_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
                $errors['report_file'] = 'Error al subir el archivo.';
            }
        }

        // Si hay errores básicos, recargamos el formulario
        if (!empty($errors)) {
            $reports   = $this->reportService->listReports();
            $pageTitle = 'Validar Reporte - ' . APP_NAME;

            include BASE_PATH . '/views/reports/validate.php';
            return;
        }

        // 3) Cargar el registro del reporte desde la BD
        $report = $this->reportService->getReportById($reportId);
        if (!$report) {
            $errors['report_id'] = 'El reporte seleccionado no existe.';
            $reports   = $this->reportService->listReports();
            $pageTitle = 'Validar Reporte - ' . APP_NAME;

            include BASE_PATH . '/views/reports/validate.php';
            return;
        }

        $storedHash = $report['pdf_hash']  ?? null;
        $hashAlgo   = $report['hash_algo'] ?? 'sha256';

        if (empty($storedHash)) {
            $errors['general'] = 'Este reporte no tiene una firma hash de PDF registrada.';
            $reports           = $this->reportService->listReports();
            $pageTitle         = 'Validar Reporte - ' . APP_NAME;

            include BASE_PATH . '/views/reports/validate.php';
            return;
        }

        if (!in_array($hashAlgo, hash_algos(), true)) {
            $errors['general'] = 'Algoritmo de hash inválido para este reporte.';
            $reports           = $this->reportService->listReports();
            $pageTitle         = 'Validar Reporte - ' . APP_NAME;

            include BASE_PATH . '/views/reports/validate.php';
            return;
        }

        // 4) Calcular hash del archivo subido
        $tmpPath   = $_FILES['report_file']['tmp_name'];
        $fileHash  = hash_file($hashAlgo, $tmpPath);

        // 5) Comparar
        if (hash_equals($storedHash, $fileHash)) {
            $isValid   = true;
            $resultMsg = 'El archivo es VÁLIDO. El contenido coincide con la firma registrada.';
        } else {
            $isValid   = false;
            $resultMsg = 'El archivo NO coincide con la firma registrada. Puede haber sido alterado.';
        }

        // Volvemos a pasar la lista de reportes para que el usuario pueda validar otro
        $reports   = $this->reportService->listReports();
        $pageTitle = 'Validar Reporte - ' . APP_NAME;

        include BASE_PATH . '/views/reports/validate.php';
    }


}
