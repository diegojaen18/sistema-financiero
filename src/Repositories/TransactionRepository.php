<?php
// src/Repositories/TransactionRepository.php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class TransactionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function findAll(): array
    {
        $sql = "SELECT 
                    t.*,
                    u.username AS created_by_username,
                    COALESCE(SUM(tl.debit), 0)  AS total_debit,
                    COALESCE(SUM(tl.credit), 0) AS total_credit
                FROM transactions t
                LEFT JOIN users u ON u.id = t.created_by
                LEFT JOIN transaction_lines tl ON tl.transaction_id = t.id
                GROUP BY t.id
                ORDER BY t.transaction_date DESC, t.id DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT 
                    t.*,
                    u.username AS created_by_username
                FROM transactions t
                LEFT JOIN users u ON u.id = t.created_by
                WHERE t.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $tx = $stmt->fetch();

        return $tx ?: null;
    }

    public function findLinesByTransactionId(int $transactionId): array
    {
        $sql = "SELECT 
                    tl.*,
                    a.code,
                    a.name,
                    a.account_type
                FROM transaction_lines tl
                INNER JOIN accounts a ON a.id = tl.account_id
                WHERE tl.transaction_id = :id
                ORDER BY tl.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $transactionId]);
        return $stmt->fetchAll();
    }

    /**
     * Crea una transacción y sus líneas (partida doble).
     * $txData: ['transaction_date', 'description', 'is_posted', 'created_by']
     * $lines: cada línea = ['account_id', 'debit', 'credit', 'memo']
     */
    public function createWithLines(array $txData, array $lines): bool
    {
        try {
            $this->db->beginTransaction();

            $sqlTx = "INSERT INTO transactions (transaction_date, description, is_posted, created_by)
                      VALUES (:transaction_date, :description, :is_posted, :created_by)";

            $stmtTx = $this->db->prepare($sqlTx);
            $stmtTx->execute([
                'transaction_date' => $txData['transaction_date'],
                'description'      => $txData['description'],
                'is_posted'        => $txData['is_posted'] ? 1 : 0,
                'created_by'       => $txData['created_by'],
            ]);

            $txId = (int) $this->db->lastInsertId();

            $sqlLine = "INSERT INTO transaction_lines (transaction_id, account_id, debit, credit, memo)
                        VALUES (:transaction_id, :account_id, :debit, :credit, :memo)";
            $stmtLine = $this->db->prepare($sqlLine);

            foreach ($lines as $line) {
                $stmtLine->execute([
                    'transaction_id' => $txId,
                    'account_id'     => $line['account_id'],
                    'debit'          => $line['debit'],
                    'credit'         => $line['credit'],
                    'memo'           => $line['memo'] ?? null,
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Solo permite borrar si la transacción NO está posteada.
     */
    public function deleteIfNotPosted(int $id): bool
    {
        $tx = $this->findById($id);
        if (!$tx || $tx['is_posted']) {
            return false;
        }

        $sqlLines = "DELETE FROM transaction_lines WHERE transaction_id = :id";
        $stmt     = $this->db->prepare($sqlLines);
        $stmt->execute(['id' => $id]);

        $sqlTx = "DELETE FROM transactions WHERE id = :id";
        $stmt  = $this->db->prepare($sqlTx);
        return $stmt->execute(['id' => $id]);
    }
}
