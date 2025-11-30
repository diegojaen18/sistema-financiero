<?php
// src/Services/AuthService.php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Security\Sanitizer;
use App\Security\Validator;
use App\Security\SessionManager;

class AuthService
{
    private UserRepository $userRepo;
    private Validator $validator;

    public function __construct()
    {
        $this->userRepo  = new UserRepository();
        $this->validator = new Validator();
    }

    public function login(array $data): array
    {
        SessionManager::start();

        $data    = Sanitizer::cleanArray($data);
        $errors  = $this->validator->validateRequired($data, ['username', 'password']);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $username = $data['username'];
        $password = $data['password'];

        $user = $this->userRepo->findByUsername($username);

        if (!$user) {
            return [
                'success' => false,
                'errors'  => ['general' => 'Usuario o contraseña incorrectos.']
            ];
        }

        // En tu script ya guardaste un password_hash (bcrypt)
        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'errors'  => ['general' => 'Usuario o contraseña incorrectos.']
            ];
        }

        // Login ok → guardamos en sesión
        SessionManager::set('user_id', $user['id']);
        SessionManager::set('username', $user['username']);
        SessionManager::set('full_name', $user['full_name']);

        // Actualizar último login
        $this->userRepo->updateLastLogin((int) $user['id']);

        return ['success' => true, 'errors' => []];
    }

    public function logout(): void
    {
        SessionManager::start();
        SessionManager::destroy();
    }
}
