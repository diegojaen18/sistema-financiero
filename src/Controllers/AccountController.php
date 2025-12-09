<?php
// src/Controllers/AccountController.php

namespace App\Controllers;

use App\Repositories\AccountRepository;
use App\Security\Validator;
use App\Security\Sanitizer;
use App\Security\SessionManager;
use App\Services\AuthorizationService;

class AccountController
{
    private AccountRepository $accountRepo;
    private Validator $validator;
    private AuthorizationService $authService;

    public function __construct()
    {
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


    private function isAuditor(): bool
    {
        $userId = SessionManager::get('user_id');
        return $this->authService->userHasRoleName($userId, 'Auditor');
    }

    public function listAccounts(string $search = ''): void
    {
        // Auditor no puede entrar ni en modo lectura
        if ($this->isAuditor()) {
            header('Location: dashboard.php?msg=noperm');
            exit;
        }

        $accounts = $this->accountRepo->findAll();
        $search   = trim($search);

        $toLower = function (string $value): string {
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($value, 'UTF-8');
            }
            return strtolower($value);
        };

        if ($search !== '') {
            $searchLower = $toLower($search);

            $accounts = array_values(array_filter($accounts, function ($a) use ($searchLower, $toLower) {
                foreach ($a as $value) {
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

        $pageTitle      = 'Catálogo de Cuentas - ' . APP_NAME;
        $currentSearch  = $search;
        $isAdminLimited = $this->isAdminLimited();

        include BASE_PATH . '/views/accounts/list.php';
    }

    public function showCreate(array $errors = [], array $oldData = []): void
    {
        if ($this->isAdminLimited() || $this->isAuditor()) {
            header('Location: accounts.php?msg=noperm');
            exit;
        }

        $pageTitle = 'Nueva Cuenta - ' . APP_NAME;
        include BASE_PATH . '/views/accounts/create.php';
    }

    public function handleCreate(): void
    {
        if ($this->isAdminLimited() || $this->isAuditor()) {
            header('Location: accounts.php?msg=noperm');
            exit;
        }

        $data   = Sanitizer::cleanArray($_POST);
        $errors = $this->validator->validateRequired(
            $data,
            ['code', 'name', 'account_class', 'account_type']
        );

        $accountClass = (int) ($data['account_class'] ?? 0);
        if ($accountClass < 1 || $accountClass > 7) {
            $errors['account_class'] = 'La clase de cuenta debe estar entre 1 y 7.';
        }

        $validTypes = ['debit', 'credit'];
        if (!in_array($data['account_type'] ?? '', $validTypes, true)) {
            $errors['account_type'] = 'El tipo de cuenta debe ser Débito o Crédito.';
        }

        if (!empty($errors)) {
            $oldData   = $data;
            $pageTitle = 'Nueva Cuenta - ' . APP_NAME;
            include BASE_PATH . '/views/accounts/create.php';
            return;
        }

        $currentUserId = SessionManager::get('user_id');

        $ok = $this->accountRepo->create([
            'code'          => $data['code'],
            'name'          => $data['name'],
            'account_class' => $accountClass,
            'account_type'  => $data['account_type'],
            'is_active'     => isset($data['is_active']) ? 1 : 0,
            'created_by'    => $currentUserId,
        ]);

        if (!$ok) {
            $errors['general'] = 'Error al crear la cuenta. Verifique que el código no esté repetido.';
            $oldData           = $data;
            $pageTitle         = 'Nueva Cuenta - ' . APP_NAME;
            include BASE_PATH . '/views/accounts/create.php';
            return;
        }

        header('Location: accounts.php?msg=created');
        exit;
    }

    public function showEdit(int $id, array $errors = [], array $oldData = []): void
    {
        if ($this->isAdminLimited() || $this->isAuditor()) {
            header('Location: accounts.php?msg=noperm');
            exit;
        }

        $account = $this->accountRepo->findById($id);
        if (!$account) {
            header('Location: accounts.php?msg=notfound');
            exit;
        }

        $pageTitle = 'Editar Cuenta - ' . APP_NAME;
        include BASE_PATH . '/views/accounts/edit.php';
    }

    public function handleEdit(int $id): void
    {
        if ($this->isAdminLimited() || $this->isAuditor()) {
            header('Location: accounts.php?msg=noperm');
            exit;
        }

        $data = Sanitizer::cleanArray($_POST);

        $errors = $this->validator->validateRequired(
            $data,
            ['code', 'name', 'account_class', 'account_type']
        );

        $accountClass = (int) ($data['account_class'] ?? 0);
        if ($accountClass < 1 || $accountClass > 7) {
            $errors['account_class'] = 'La clase de cuenta debe estar entre 1 y 7.';
        }

        $validTypes = ['debit', 'credit'];
        if (!in_array($data['account_type'] ?? '', $validTypes, true)) {
            $errors['account_type'] = 'El tipo de cuenta debe ser Débito o Crédito.';
        }

        if (!empty($errors)) {
            $account   = $this->accountRepo->findById($id);
            $oldData   = $data;
            $pageTitle = 'Editar Cuenta - ' . APP_NAME;
            include BASE_PATH . '/views/accounts/edit.php';
            return;
        }

        $ok = $this->accountRepo->update($id, [
            'code'          => $data['code'],
            'name'          => $data['name'],
            'account_class' => $accountClass,
            'account_type'  => $data['account_type'],
            'is_active'     => isset($data['is_active']) ? 1 : 0,
        ]);

        if (!$ok) {
            $errors['general'] = 'Error al actualizar la cuenta. Verifique datos duplicados.';
            $account           = $this->accountRepo->findById($id);
            $oldData           = $data;
            $pageTitle         = 'Editar Cuenta - ' . APP_NAME;
            include BASE_PATH . '/views/accounts/edit.php';
            return;
        }

        header('Location: accounts.php?msg=updated');
        exit;
    }

    public function toggleStatus(int $id): void
    {
        if ($this->isAdminLimited() || $this->isAuditor()) {
            header('Location: accounts.php?msg=noperm');
            exit;
        }

        $account = $this->accountRepo->findById($id);
        if (!$account) {
            header('Location: accounts.php?msg=notfound');
            exit;
        }

        $newStatus = !$account['is_active'];
        $this->accountRepo->setActive($id, $newStatus);

        header('Location: accounts.php?msg=status');
        exit;
    }
}
