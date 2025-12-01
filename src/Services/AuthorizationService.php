<?php
// src/Services/AuthorizationService.php

namespace App\Services;

use App\Database\Connection;
use PDO;

class AuthorizationService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function userHasRoleName(int $userId, string $roleName): bool
    {
        $sql = "SELECT 1
                FROM user_roles ur
                INNER JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = :user_id
                  AND r.name = :role_name
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'   => $userId,
            'role_name' => $roleName,
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
