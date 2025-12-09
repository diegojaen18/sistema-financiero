<?php
// src/Controllers/TransactionController.php

namespace App\Controllers;

use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use App\Security\Validator;
use App\Security\Sanitizer;
use App\Security\SessionManager;
use App\Services\AuthorizationService;

class TransactionController
{
    private TransactionRepository $txRepo;
    private AccountRepository $accountRepo;
    private Validator $validator;
    private AuthorizationService $authService;

    public function __construct()
    {
        $this->txRepo      = new TransactionRepository();
        $this->accountRepo = new AccountRepository();
        $this->validator   = new Validator();
        $this->authService = new AuthorizationService();
    }

    private function isAdminLimited(): bool
    {
        $userId = SessionManager::get('user_id');
        return $this->authService->userHasRoleName($userId, 'Administrador')
            || $this->authService->userHasRoleName($userId, 'Gerente Financiero');
    }


    public function listTransactions(string $search = ''): void
    {
        $transactions = $this->txRepo->findAll();
        $search       = trim($search);

        $toLower = function (string $value): string {
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($value, 'UTF-8');
            }
            return strtolower($value);
        };

        if ($search !== '') {
            $searchLower = $toLower($search);

            $transactions = array_values(array_filter($transactions, function ($t) use ($searchLower, $toLower) {
                foreach ($t as $value) {
                    if (is_scalar($value)) {
                        $valueLower = $toLower((string)$value);
                        if (strpos($valueLower, $searchLower) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            }));
        }

        $pageTitle      = 'Diario General - ' . APP_NAME;
        $currentSearch  = $search;
        $isAdminLimited = $this->isAdminLimited();

        include BASE_PATH . '/views/transactions/list.php';
    }

    public function showCreate(array $errors = [], array $oldData = []): void
    {
        if ($this->isAdminLimited()) {
            header('Location: transactions.php?msg=noperm');
            exit;
        }

        $accounts  = $this->accountRepo->findActive();
        $pageTitle = 'Nueva Transacción - ' . APP_NAME;
        include BASE_PATH . '/views/transactions/create.php';
    }

    public function handleCreate(): void
    {
        if ($this->isAdminLimited()) {
            header('Location: transactions.php?msg=noperm');
            exit;
        }

        $data   = Sanitizer::cleanArray($_POST);
        $errors = $this->validator->validateRequired(
            $data,
            ['transaction_date', 'description', 'debit_account_id', 'credit_account_id', 'amount']
        );

        $amount = isset($data['amount']) ? (float)$data['amount'] : 0;

        if ($amount <= 0) {
            $errors['amount'] = 'El monto debe ser mayor que cero.';
        }

        $debitAccountId  = (int)($data['debit_account_id'] ?? 0);
        $creditAccountId = (int)($data['credit_account_id'] ?? 0);

        if ($debitAccountId <= 0) {
            $errors['debit_account_id'] = 'Debe seleccionar una cuenta de débito.';
        }

        if ($creditAccountId <= 0) {
            $errors['credit_account_id'] = 'Debe seleccionar una cuenta de crédito.';
        }

        if ($debitAccountId > 0 && $debitAccountId === $creditAccountId) {
            $errors['credit_account_id'] = 'La cuenta de débito y crédito no pueden ser la misma.';
        }

        // Si ya hay errores básicos, devolvemos enseguida
        if (!empty($errors)) {
            $accounts  = $this->accountRepo->findActive();
            $oldData   = $data;
            $pageTitle = 'Nueva Transacción - ' . APP_NAME;
            include BASE_PATH . '/views/transactions/create.php';
            return;
        }

        // -----------------------------
        // REGLA DE NEGOCIO ANTI "DINERO DE LA NADA"
        // -----------------------------
        // 1) Cargar cuentas
        $debitAccount  = $this->accountRepo->findById($debitAccountId);
        $creditAccount = $this->accountRepo->findById($creditAccountId);

        if (!$debitAccount || !$creditAccount) {
            $errors['general'] = 'Alguna de las cuentas seleccionadas no existe.';
        } else {
            // 2) Obtener totales actuales de cada cuenta
            $debitTotals  = $this->txRepo->getTotalsByAccountId($debitAccountId);
            $creditTotals = $this->txRepo->getTotalsByAccountId($creditAccountId);

            // Función para obtener el saldo actual según la naturaleza de la cuenta
            $computeBalance = function (array $account, array $totals): float {
                $type        = $account['account_type'] ?? 'debit'; // 'debit' o 'credit'
                $totalDebit  = (float)($totals['total_debit'] ?? 0);
                $totalCredit = (float)($totals['total_credit'] ?? 0);

                if ($type === 'debit') {
                    return $totalDebit - $totalCredit;
                }

                // account_type === 'credit'
                return $totalCredit - $totalDebit;
            };

            $debitBalanceCurrent  = $computeBalance($debitAccount,  $debitTotals);
            $creditBalanceCurrent = $computeBalance($creditAccount, $creditTotals);

            // Función para simular el efecto de la nueva transacción sobre el saldo
            $applyEffect = function (array $account, float $currentBalance, float $amount, bool $isDebitLine): float {
                $type = $account['account_type'] ?? 'debit';

                if ($type === 'debit') {
                    // Naturaleza deudora
                    // Línea en débito => aumenta saldo
                    // Línea en crédito => disminuye saldo
                    return $currentBalance + ($isDebitLine ? $amount : -$amount);
                }

                // Naturaleza acreedora
                // Línea en débito => disminuye saldo
                // Línea en crédito => aumenta saldo
                return $currentBalance + ($isDebitLine ? -$amount : $amount);
            };

            // 3) Simular nuevo saldo después de la operación
            $debitBalanceNew  = $applyEffect($debitAccount,  $debitBalanceCurrent,  $amount, true);   // línea al DEBE
            $creditBalanceNew = $applyEffect($creditAccount, $creditBalanceCurrent, $amount, false);  // línea al HABER

            // 4) Regla: para cuentas de clase 1 (Activos), no se permite saldo negativo
            $debitClass  = (int)($debitAccount['account_class'] ?? 0);
            $creditClass = (int)($creditAccount['account_class'] ?? 0);

            if ($debitClass === 1 && $debitBalanceNew < 0) {
                $errors['debit_account_id'] = 'La cuenta de débito no tiene saldo suficiente para esta operación (no se permiten saldos negativos en activos).';
            }

            if ($creditClass === 1 && $creditBalanceNew < 0) {
                $errors['credit_account_id'] = 'La cuenta de crédito no tiene saldo suficiente para esta operación (no se permiten saldos negativos en activos).';
            }
        }

        // Si la regla de negocio falla, devolvemos al formulario
        if (!empty($errors)) {
            $accounts  = $this->accountRepo->findActive();
            $oldData   = $data;
            $pageTitle = 'Nueva Transacción - ' . APP_NAME;
            include BASE_PATH . '/views/transactions/create.php';
            return;
        }

        // -----------------------------
        // Si todo está OK, registramos la transacción (partida doble)
        // -----------------------------

        $currentUserId = SessionManager::get('user_id');

        $lines = [
            [
                'account_id' => $debitAccountId,
                'debit'      => $amount,
                'credit'     => 0,
                'memo'       => 'Débito',
            ],
            [
                'account_id' => $creditAccountId,
                'debit'      => 0,
                'credit'     => $amount,
                'memo'       => 'Crédito',
            ],
        ];

        $txData = [
            'transaction_date' => $data['transaction_date'],
            'description'      => $data['description'],
            'is_posted'        => true,
            'created_by'       => $currentUserId,
        ];

        $ok = $this->txRepo->createWithLines($txData, $lines);

        if (!$ok) {
            $accounts           = $this->accountRepo->findActive();
            $errors['general']  = 'Error al crear la transacción. Revise los datos.';
            $oldData            = $data;
            $pageTitle          = 'Nueva Transacción - ' . APP_NAME;
            include BASE_PATH . '/views/transactions/create.php';
            return;
        }

        header('Location: transactions.php?msg=created');
        exit;
    }

    public function view(int $id): void
    {
        // Ver está permitido incluso para admin
        $transaction = $this->txRepo->findById($id);
        if (!$transaction) {
            header('Location: transactions.php?msg=notfound');
            exit;
        }

        $lines     = $this->txRepo->findLinesByTransactionId($id);
        $pageTitle = 'Detalle Transacción - ' . APP_NAME;
        include BASE_PATH . '/views/transactions/view.php';
    }

    public function delete(int $id): void
    {
        // Admin NO puede borrar transacciones
        if ($this->isAdminLimited()) {
            header('Location: transactions.php?msg=noperm');
            exit;
        }

        $ok = $this->txRepo->deleteIfNotPosted($id);

        if ($ok) {
            header('Location: transactions.php?msg=deleted');
        } else {
            header('Location: transactions.php?msg=notdeleted');
        }
        exit;
    }
}
