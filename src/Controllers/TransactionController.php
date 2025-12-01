<?php
// src/Controllers/TransactionController.php

namespace App\Controllers;

use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use App\Security\Validator;
use App\Security\Sanitizer;
use App\Security\SessionManager;

class TransactionController
{
    private TransactionRepository $txRepo;
    private AccountRepository $accountRepo;
    private Validator $validator;

    public function __construct()
    {
        $this->txRepo      = new TransactionRepository();
        $this->accountRepo = new AccountRepository();
        $this->validator   = new Validator();
    }

    public function listTransactions(): void
    {
        $transactions = $this->txRepo->findAll();
        $pageTitle    = 'Diario General - ' . APP_NAME;
        include BASE_PATH . '/views/transactions/list.php';
    }

    public function showCreate(array $errors = [], array $oldData = []): void
    {
        $accounts  = $this->accountRepo->findActive();
        $pageTitle = 'Nueva Transacción - ' . APP_NAME;
        include BASE_PATH . '/views/transactions/create.php';
    }

    public function handleCreate(): void
    {
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

        if (!empty($errors)) {
            $accounts  = $this->accountRepo->findActive();
            $oldData   = $data;
            $pageTitle = 'Nueva Transacción - ' . APP_NAME;
            include BASE_PATH . '/views/transactions/create.php';
            return;
        }

        $currentUserId = SessionManager::get('user_id');

        // Validamos partida doble: mismo monto para débito y crédito
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
            'is_posted'        => true,   // se registra como posteada
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
        $ok = $this->txRepo->deleteIfNotPosted($id);

        if ($ok) {
            header('Location: transactions.php?msg=deleted');
        } else {
            header('Location: transactions.php?msg=notdeleted');
        }
        exit;
    }
}
