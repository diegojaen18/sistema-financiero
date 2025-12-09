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
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updateLastLogin(int $userId): void
    {
        $sql  = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    /* === NUEVO: CRUD BÁSICO === */

    public function findAll(): array
    {
        $sql = "SELECT id, username, full_name, email, is_active, created_at 
                FROM users
                ORDER BY id ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql  = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

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

    public function search(string $search = ''): array
    {
        $search = trim($search);

        if ($search === '') {
            // comportamiento por defecto
            return $this->findAll();
        }

        $sql = "
            SELECT *
            FROM users
            WHERE username  LIKE :term
            OR full_name LIKE :term
            OR email     LIKE :term
            ORDER BY id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $like = '%' . $search . '%';
        $stmt->bindValue(':term', $like, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
