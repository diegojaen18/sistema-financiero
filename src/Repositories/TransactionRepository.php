<?php
namespace SistemaFinanciero\Repositories;

class TransactionRepository extends BaseRepository {
    
    protected string $table = 'transactions';
    
    public function findWithLines(int $id): ?array {
        $transaction = $this->find($id);
        
        if ($transaction) {
            $transaction['lines'] = $this->getLines($id);
        }
        
        return $transaction;
    }
    
    public function getLines(int $transactionId): array {
        $sql = "SELECT tl.*, a.code as account_code, a.name as account_name
                FROM transaction_lines tl
                INNER JOIN accounts a ON tl.account_id = a.id
                WHERE tl.transaction_id = ?
                ORDER BY tl.id";
        
        return $this->db->query($sql, [$transactionId]);
    }
    
    public function saveWithLines(array $transactionData, array $lines): int {
        $this->db->beginTransaction();
        
        try {
            $transactionId = $this->save($transactionData);
            
            foreach ($lines as $line) {
                $line['transaction_id'] = $transactionId;
                $this->saveLine($line);
            }
            
            $this->db->commit();
            return $transactionId;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function saveLine(array $lineData): int {
        $sql = "INSERT INTO transaction_lines (transaction_id, account_id, debit, credit, memo)
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->execute($sql, [
            $lineData['transaction_id'],
            $lineData['account_id'],
            $lineData['debit'] ?? 0,
            $lineData['credit'] ?? 0,
            $lineData['memo'] ?? ''
        ]);
        
        return (int)$this->db->lastInsertId();
    }
    
    public function findByDateRange(string $startDate, string $endDate): array {
        $sql = "SELECT t.*, u.full_name as created_by_name,
                (SELECT SUM(debit) FROM transaction_lines WHERE transaction_id = t.id) as total_debit,
                (SELECT SUM(credit) FROM transaction_lines WHERE transaction_id = t.id) as total_credit
                FROM {$this->table} t
                INNER JOIN users u ON t.created_by = u.id
                WHERE t.transaction_date BETWEEN ? AND ?
                ORDER BY t.transaction_date DESC, t.id DESC";
        
        return $this->db->query($sql, [$startDate, $endDate]);
    }
    
    public function findPosted(): array {
        $sql = "SELECT t.*, u.full_name as created_by_name
                FROM {$this->table} t
                INNER JOIN users u ON t.created_by = u.id
                WHERE t.is_posted = 1
                ORDER BY t.transaction_date DESC";
        
        return $this->db->query($sql);
    }
}