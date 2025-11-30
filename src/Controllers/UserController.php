<?php
// src/Controllers/UserController.php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Security\Validator;
use App\Security\Sanitizer;
use App\Security\SessionManager;

class UserController
{
    private UserRepository $userRepo;
    private Validator $validator;

    public function __construct()
    {
        $this->userRepo  = new UserRepository();
        $this->validator = new Validator();
    }

    public function listUsers(): void
    {
        $users     = $this->userRepo->findAll();
        $pageTitle = 'Usuarios - ' . APP_NAME;
        include BASE_PATH . '/views/users/list.php';
    }

    public function showCreate(array $errors = [], array $oldData = []): void
    {
        $pageTitle = 'Nuevo usuario - ' . APP_NAME;
        include BASE_PATH . '/views/users/create.php';
    }

    public function handleCreate(): void
    {
        $data   = Sanitizer::cleanArray($_POST);
        $errors = $this->validator->validateRequired(
            $data,
            ['username', 'full_name', 'email', 'password', 'password_confirmation']
        );

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no es válido.';
        }

        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errors)) {
            $oldData   = $data;
            $pageTitle = 'Nuevo usuario - ' . APP_NAME;
            include BASE_PATH . '/views/users/create.php';
            return;
        }

        $currentUserId = SessionManager::get('user_id');

        $ok = $this->userRepo->create([
            'username'   => $data['username'],
            'full_name'  => $data['full_name'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'is_active'  => isset($data['is_active']) ? 1 : 0,
            'created_by' => $currentUserId,
        ]);

        if (!$ok) {
            $errors['general'] = 'Error al crear el usuario. Verifique que el usuario y el correo no estén repetidos.';
            $oldData           = $data;
            $pageTitle         = 'Nuevo usuario - ' . APP_NAME;
            include BASE_PATH . '/views/users/create.php';
            return;
        }

        header('Location: users.php?msg=created');
        exit;
    }

    public function showEdit(int $id, array $errors = [], array $oldData = []): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            header('Location: users.php?msg=notfound');
            exit;
        }

        $pageTitle = 'Editar usuario - ' . APP_NAME;
        include BASE_PATH . '/views/users/edit.php';
    }

    public function handleEdit(int $id): void
    {
        $data = Sanitizer::cleanArray($_POST);

        $errors = $this->validator->validateRequired(
            $data,
            ['username', 'full_name', 'email']
        );

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no es válido.';
        }

        if (!empty($data['password']) || !empty($data['password_confirmation'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
            }
        }

        if (!empty($errors)) {
            $user      = $this->userRepo->findById($id);
            $oldData   = $data;
            $pageTitle = 'Editar usuario - ' . APP_NAME;
            include BASE_PATH . '/views/users/edit.php';
            return;
        }

        $ok = $this->userRepo->update($id, [
            'username'  => $data['username'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'password'  => $data['password'] ?? '',
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ]);

        if (!$ok) {
            $errors['general'] = 'Error al actualizar el usuario. Verifique datos duplicados.';
            $user              = $this->userRepo->findById($id);
            $oldData           = $data;
            $pageTitle         = 'Editar usuario - ' . APP_NAME;
            include BASE_PATH . '/views/users/edit.php';
            return;
        }

        header('Location: users.php?msg=updated');
        exit;
    }

    public function toggleStatus(int $id): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            header('Location: users.php?msg=notfound');
            exit;
        }

        $newStatus = !$user['is_active'];
        $this->userRepo->setActive($id, $newStatus);

        header('Location: users.php?msg=status');
        exit;
    }
}
