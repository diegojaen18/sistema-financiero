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

    public function index(): void
    {
        $userId   = SessionManager::get('user_id');
        $reports  = $this->reportService->listReports();
        $canSign  = $this->authService->userHasRoleName($userId, 'Gerente Financiero');
        $pageTitle = 'Informes Financieros - ' . APP_NAME;

        include BASE_PATH . '/views/reports/list.php';
    }

    public function incomeStatement(): void
    {
        $pageTitle  = 'Estado de Resultados - ' . APP_NAME;
        $errors     = [];
        $reportData = null;

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
                $reportData = $result['data'];
            }
        }

        include BASE_PATH . '/views/reports/income_statement.php';
    }

    public function balanceSheet(): void
    {
        $pageTitle  = 'Balance General - ' . APP_NAME;
        $errors     = [];
        $reportData = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = Sanitizer::cleanArray($_POST);
            $errors = $this->validator->validateRequired($data, ['as_of_date']);

            $asOfDate = $data['as_of_date'] ?? '';

            if (empty($errors)) {
                $userId = SessionManager::get('user_id');
                $result = $this->reportService->generateBalanceSheet($asOfDate, $userId);
                $reportData = $result['data'];
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

        $ok = $this->reportService->signReport($id, $userId);

        if ($ok) {
            header('Location: reports.php?msg=signed');
        } else {
            header('Location: reports.php?msg=signerror');
        }
        exit;
    }
}
