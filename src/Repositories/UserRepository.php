<?php
// src/Repositories/UserRepository.php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function findByUsername(string $username): ?array
    {
        $sql  = "SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function updateLastLogin(int $userId): void
    {
        $sql  = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    /* === CRUD BÁSICO === */

    public function findAll(): array
    {
        // Seleccionamos TODO para evitar problemas con columnas opcionales
        $sql = "SELECT * FROM users ORDER BY id ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql  = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO users (username, password_hash, full_name, email, is_active, created_by)
                VALUES (:username, :password_hash, :full_name, :email, :is_active, :created_by)";

        $stmt = $this->db->prepare($sql);

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            return $stmt->execute([
                'username'      => $data['username'],
                'password_hash' => $passwordHash,
                'full_name'     => $data['full_name'],
                'email'         => $data['email'],
                'is_active'     => $data['is_active'] ?? 1,
                'created_by'    => $data['created_by'] ?? null,
            ]);
        } catch (\PDOException $e) {
            // Aquí podrías loguear el error si quieres
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        $params = [
            'username'  => $data['username'],
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'is_active' => $data['is_active'] ?? 1,
            'id'        => $id,
        ];

        $sql = "UPDATE users 
                SET username = :username,
                    full_name = :full_name,
                    email = :email,
                    is_active = :is_active";

        if (!empty($data['password'])) {
            $sql .= ", password_hash = :password_hash";
            $params['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // NO eliminamos físicamente usuarios: solo activamos/desactivamos
    public function setActive(int $id, bool $isActive): bool
    {
        $sql  = "UPDATE users SET is_active = :active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'active' => $isActive ? 1 : 0,
            'id'     => $id,
        ]);
    }

    /**
     * Búsqueda simple por username, full_name o email.
     * Para evitar más problemas de SQL, si hay lío simplemente devolvemos findAll().
     */
    public function search(string $search = ''): array
    {
        $search = trim($search);

        // Si no hay término de búsqueda, devolvemos todo
        if ($search === '') {
            return $this->findAll();
        }

        // Traemos todos los usuarios y filtramos en PHP (igual que en transacciones)
        $allUsers = $this->findAll();

        $toLower = function (string $value): string {
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($value, 'UTF-8');
            }
            return strtolower($value);
        };

        $term = $toLower($search);
        $filtered = [];

        foreach ($allUsers as $user) {
            $haystack = $toLower(
                ($user['username']  ?? '') . ' ' .
                ($user['full_name'] ?? '') . ' ' .
                ($user['email']     ?? '')
            );

            if (strpos($haystack, $term) !== false) {
                $filtered[] = $user;
            }
        }

        return $filtered;
    }

}
