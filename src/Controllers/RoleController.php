<?php
// src/Controllers/RoleController.php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Services\AuthorizationService;
use App\Security\SessionManager;
use App\Security\Sanitizer;

class RoleController
{
    private UserRepository $userRepo;
    private RoleRepository $roleRepo;
    private AuthorizationService $authService;

    public function __construct()
    {
        $this->userRepo   = new UserRepository();
        $this->roleRepo   = new RoleRepository();
        $this->authService = new AuthorizationService();
    }

    private function requireAdmin(): void
    {
        $userId = SessionManager::get('user_id');
        if (!$this->authService->userHasRoleName($userId, 'Administrador')) {
            header('Location: dashboard.php');
            exit;
        }
    }

    public function listUsers(): void
    {
        $this->requireAdmin();

        $users = $this->userRepo->findAll();
        $rolesByUser = [];

        foreach ($users as $u) {
            $rolesByUser[$u['id']] = $this->roleRepo->getUserRolesByUser($u['id']);
        }

        $pageTitle = 'Roles de Usuarios - ' . APP_NAME;
        include BASE_PATH . '/views/roles/users.php';
    }

    public function editUserRoles(int $userId): void
    {
        $this->requireAdmin();

        $user = $this->userRepo->findById($userId);
        if (!$user) {
            header('Location: roles.php?msg=notfound');
            exit;
        }

        $roles       = $this->roleRepo->findAll();
        $userRoleIds = $this->roleRepo->getUserRoleIds($userId);

        $pageTitle = 'Gestionar roles - ' . APP_NAME;
        include BASE_PATH . '/views/roles/manage.php';
    }

    public function updateUserRoles(int $userId): void
    {
        $this->requireAdmin();

        $user = $this->userRepo->findById($userId);
        if (!$user) {
            header('Location: roles.php?msg=notfound');
            exit;
        }

        $data      = Sanitizer::cleanArray($_POST);
        $roleIds   = isset($data['roles']) ? array_map('intval', (array)$data['roles']) : [];
        $adminId   = SessionManager::get('user_id');

        $this->roleRepo->setUserRoles($userId, $roleIds, $adminId);

        header('Location: roles.php?msg=updated');
        exit;
    }
}
