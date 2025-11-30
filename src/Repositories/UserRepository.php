<?php
namespace SistemaFinanciero\Repositories;

class UserRepository extends BaseRepository {
    
    protected string $table = 'users';
    
    public function findByUsername(string $username): ?array {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.username = ?
                GROUP BY u.id";
        
        return $this->db->queryOne($sql, [$username]);
    }
    
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        return $this->db->queryOne($sql, [$email]);
    }
    
    public function findActive(): array {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY full_name";
        return $this->db->query($sql);
    }
    
    public function usernameExists(string $username, ?int $exceptId = null): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }
        
        return $this->db->queryScalar($sql, $params) > 0;
    }
    
    public function emailExists(string $email, ?int $exceptId = null): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }
        
        return $this->db->queryScalar($sql, $params) > 0;
    }
    
    public function softDelete(int $id): bool {
        return $this->update($id, ['is_active' => 0]);
    }
    
    public function hasActivity(int $userId): bool {
        $sql = "SELECT COUNT(*) FROM transactions WHERE created_by = ?";
        if ($this->db->queryScalar($sql, [$userId]) > 0) {
            return true;
        }
        
        $sql = "SELECT COUNT(*) FROM accounts WHERE created_by = ?";
        if ($this->db->queryScalar($sql, [$userId]) > 0) {
            return true;
        }
        
        return false;
    }
}