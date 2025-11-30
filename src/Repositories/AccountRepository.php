<?php
namespace SistemaFinanciero\Repositories;

class AccountRepository extends BaseRepository {
    
    protected string $table = 'accounts';
    
    public function findByCode(string $code): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE code = ?";
        return $this->db->queryOne($sql, [$code]);
    }
    
    public function findByClass(int $class): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE account_class = ? AND is_active = 1 
                ORDER BY code";
        return $this->db->query($sql, [$class]);
    }
    
    public function findActive(): array {
        $sql = "SELECT a.*, u.full_name as created_by_name
                FROM {$this->table} a
                INNER JOIN users u ON a.created_by = u.id
                WHERE a.is_active = 1
                ORDER BY a.code";
        return $this->db->query($sql);
    }
    
    public function codeExists(string $code, ?int $exceptId = null): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = ?";
        $params = [$code];
        
        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }
        
        return $this->db->queryScalar($sql, $params) > 0;
    }
    
    public function getBalancesByClass(int $class, string $startDate, string $endDate): array {
        $sql = "SELECT 
                    a.id,
                    a.code,
                    a.name,
                    a.account_type,
                    COALESCE(SUM(
                        CASE 
                            WHEN a.account_type = 'debit' THEN tl.debit - tl.credit
                            ELSE tl.credit - tl.debit
                        END
                    ), 0) as balance
                FROM accounts a
                LEFT JOIN transaction_lines tl ON a.id = tl.account_id
                LEFT JOIN transactions t ON tl.transaction_id = t.id
                WHERE a.account_class = ?
                  AND a.is_active = 1
                  AND (t.transaction_date BETWEEN ? AND ? OR t.id IS NULL)
                  AND (t.is_posted = 1 OR t.id IS NULL)
                GROUP BY a.id, a.code, a.name, a.account_type
                HAVING balance != 0
                ORDER BY a.code";
        
        return $this->db->query($sql, [$class, $startDate, $endDate]);
    }
}