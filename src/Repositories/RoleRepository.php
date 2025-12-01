<?php
// src/Repositories/RoleRepository.php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class RoleRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function findAll(): array
    {
        $sql = "SELECT id, name, description FROM roles ORDER BY id ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getUserRolesByUser(int $userId): array
    {
        $sql = "SELECT r.id, r.name, r.description
                FROM user_roles ur
                INNER JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = :user_id
                ORDER BY r.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getUserRoleIds(int $userId): array
    {
        $sql = "SELECT role_id FROM user_roles WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();

        return array_map('intval', array_column($rows, 'role_id'));
    }

    public function setUserRoles(int $userId, array $roleIds, ?int $assignedBy = null): void
    {
        try {
            $this->db->beginTransaction();

            // Borrar roles actuales
            $del = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
            $del->execute(['user_id' => $userId]);

            if (!empty($roleIds)) {
                $ins = $this->db->prepare(
                    "INSERT INTO user_roles (user_id, role_id, assigned_by)
                     VALUES (:user_id, :role_id, :assigned_by)"
                );

                foreach ($roleIds as $roleId) {
                    $ins->execute([
                        'user_id'    => $userId,
                        'role_id'    => $roleId,
                        'assigned_by'=> $assignedBy,
                    ]);
                }
            }

            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();
        }
    }
}
