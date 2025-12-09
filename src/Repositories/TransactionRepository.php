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

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
        $tx = $stmt->fetch(PDO::FETCH_ASSOC);

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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            // Aquí podrías loguear el error si quieres
            return false;
        }
    }

    /**
     * Solo permite borrar si la transacción NO está posteada.
     */
    public function deleteIfNotPosted(int $id): bool
    {
        $tx = $this->findById($id);
        if (!$tx || !empty($tx['is_posted'])) {
            return false;
        }

        $sqlLines = "DELETE FROM transaction_lines WHERE transaction_id = :id";
        $stmt     = $this->db->prepare($sqlLines);
        $stmt->execute(['id' => $id]);

        $sqlTx = "DELETE FROM transactions WHERE id = :id";
        $stmt  = $this->db->prepare($sqlTx);
        return $stmt->execute(['id' => $id]);
    }

    public function search(string $search = ''): array
    {
        $search = trim($search);

        if ($search === '') {
            return $this->findAll();
        }

        $sql = "
            SELECT
                t.*,
                u.username AS created_by_username,
                COALESCE(SUM(tl.debit), 0)  AS total_debit,
                COALESCE(SUM(tl.credit), 0) AS total_credit
            FROM transactions t
            LEFT JOIN users u ON u.id = t.created_by
            LEFT JOIN transaction_lines tl ON tl.transaction_id = t.id
            WHERE t.description LIKE :term
               OR t.reference   LIKE :term
               OR t.transaction_date LIKE :term
            GROUP BY t.id
            ORDER BY t.transaction_date DESC, t.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $like = '%' . $search . '%';
        $stmt->bindValue(':term', $like, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve los totales de débitos y créditos acumulados para una cuenta.
     * Se usa para calcular el saldo actual antes de registrar una nueva transacción.
     */
    public function getTotalsByAccountId(int $accountId): array
    {
        $sql = "
            SELECT
                COALESCE(SUM(debit), 0)  AS total_debit,
                COALESCE(SUM(credit), 0) AS total_credit
            FROM transaction_lines
            WHERE account_id = :account_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['account_id' => $accountId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [
                'total_debit'  => 0.0,
                'total_credit' => 0.0,
            ];
        }

        return [
            'total_debit'  => (float)$row['total_debit'],
            'total_credit' => (float)$row['total_credit'],
        ];
    }
}
