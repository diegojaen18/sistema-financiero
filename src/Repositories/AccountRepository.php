<?php
// src/Repositories/AccountRepository.php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class AccountRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function findAll(): array
    {
        $sql = "SELECT a.*, u.username AS created_by_username
                FROM accounts a
                LEFT JOIN users u ON u.id = a.created_by
                ORDER BY a.code ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql  = "SELECT a.*, u.username AS created_by_username
                 FROM accounts a
                 LEFT JOIN users u ON u.id = a.created_by
                 WHERE a.id = :id
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $account = $stmt->fetch();

        return $account ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO accounts 
                    (code, name, account_class, account_type, is_active, created_by)
                VALUES 
                    (:code, :name, :account_class, :account_type, :is_active, :created_by)";

        $stmt = $this->db->prepare($sql);

        try {
            return $stmt->execute([
                'code'          => $data['code'],
                'name'          => $data['name'],
                'account_class' => $data['account_class'],
                'account_type'  => $data['account_type'],
                'is_active'     => $data['is_active'] ?? 1,
                'created_by'    => $data['created_by'] ?? null,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE accounts
                SET code = :code,
                    name = :name,
                    account_class = :account_class,
                    account_type = :account_type,
                    is_active = :is_active
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        try {
            return $stmt->execute([
                'code'          => $data['code'],
                'name'          => $data['name'],
                'account_class' => $data['account_class'],
                'account_type'  => $data['account_type'],
                'is_active'     => $data['is_active'] ?? 1,
                'id'            => $id,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function setActive(int $id, bool $isActive): bool
    {
        $sql  = "UPDATE accounts SET is_active = :active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'active' => $isActive ? 1 : 0,
            'id'     => $id,
        ]);
    }

    public function findActive(): array
    {
        $sql = "SELECT * FROM accounts WHERE is_active = 1 ORDER BY code ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function search(string $search = ''): array
    {
        $search = trim($search);

        if ($search === '') {
            return $this->findAll();
        }

        $sql = "
            SELECT *
            FROM accounts
            WHERE code LIKE :term
            OR name LIKE :term
            ORDER BY code
        ";

        $stmt = $this->db->prepare($sql);
        $like = '%' . $search . '%';
        $stmt->bindValue(':term', $like, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


}
