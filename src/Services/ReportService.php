<?php
// src/Services/ReportService.php

namespace App\Services;

use App\Database\Connection;
use PDO;

class ReportService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /* ========================
     *  LISTAR Y OBTENER REPORTES
     * ======================== */

    public function listReports(): array
    {
        $sql = "SELECT 
                    r.*,
                    u.username  AS generated_by_username,
                    s.user_id   AS signed_by_id,
                    s.signature_hash,
                    s.signed_at,
                    su.username AS signed_by_username
                FROM reports r
                LEFT JOIN users u  ON u.id  = r.generated_by
                LEFT JOIN report_signatures s ON s.report_id = r.id
                LEFT JOIN users su ON su.id = s.user_id
                ORDER BY r.generated_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function getReportById(int $id): ?array
    {
        $sql = "SELECT 
                    r.*,
                    u.username  AS generated_by_username,
                    s.user_id   AS signed_by_id,
                    s.signature_hash,
                    s.signed_at,
                    su.username AS signed_by_username
                FROM reports r
                LEFT JOIN users u  ON u.id  = r.generated_by
                LEFT JOIN report_signatures s ON s.report_id = r.id
                LEFT JOIN users su ON su.id = s.user_id
                WHERE r.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /* ========================
     *  ESTADO DE RESULTADOS
     * ======================== */

    public function generateIncomeStatement(string $periodStart, string $periodEnd, int $userId): array
    {
        // Datos por cuenta dentro del período
        $sql = "SELECT 
                    a.code,
                    a.name,
                    a.account_class,
                    a.account_type,
                    SUM(
                        CASE 
                            WHEN a.account_type = 'credit' THEN tl.credit - tl.debit
                            ELSE tl.debit - tl.credit
                        END
                    ) AS amount
                FROM accounts a
                INNER JOIN transaction_lines tl ON a.id = tl.account_id
                INNER JOIN transactions t       ON t.id = tl.transaction_id
                WHERE t.is_posted = 1
                  AND a.account_class IN (4,5,6,7)
                  AND t.transaction_date BETWEEN :start AND :end
                GROUP BY a.id, a.code, a.name, a.account_class, a.account_type
                ORDER BY a.code";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $periodStart,
            'end'   => $periodEnd,
        ]);

        $rows = $stmt->fetchAll();

        $totalIncome  = 0.0;
        $totalExpense = 0.0;

        foreach ($rows as $row) {
            $amount = (float) $row['amount'];
            if ((int)$row['account_class'] === 4) {
                $totalIncome += $amount;
            } else {
                $totalExpense += $amount;
            }
        }

        $netIncome = $totalIncome - $totalExpense;

        $reportData = [
            'type'          => 'income_statement',
            'title'         => 'Estado de Resultados',
            'period_start'  => $periodStart,
            'period_end'    => $periodEnd,
            'lines'         => $rows,
            'total_income'  => $totalIncome,
            'total_expense' => $totalExpense,
            'net_income'    => $netIncome,
        ];

        $json = json_encode($reportData, JSON_UNESCAPED_UNICODE);
        $hash = hash('sha256', $json);

        $this->markPreviousModifiedIfChanged('income_statement', $periodStart, $periodEnd, $hash);

        $insertSql = "INSERT INTO reports 
                        (report_type, period_start, period_end, report_data, hash, generated_by)
                      VALUES 
                        ('income_statement', :start, :end, :data, :hash, :user_id)";

        $stmtInsert = $this->db->prepare($insertSql);
        $stmtInsert->execute([
            'start'   => $periodStart,
            'end'     => $periodEnd,
            'data'    => $json,
            'hash'    => $hash,
            'user_id' => $userId,
        ]);

        $reportId = (int) $this->db->lastInsertId();
        $report   = $this->getReportById($reportId);

        return [
            'data'   => $reportData,
            'report' => $report,
        ];
    }

    /* ========================
     *  BALANCE GENERAL
     * ======================== */

    public function generateBalanceSheet(string $asOfDate, int $userId): array
    {
        // Tomamos los saldos actuales de las cuentas (1,2,3)
        $sql = "SELECT 
                    code,
                    name,
                    account_class,
                    balance AS amount
                FROM accounts
                WHERE account_class IN (1,2,3)
                  AND is_active = 1
                ORDER BY code";

        $rows = $this->db->query($sql)->fetchAll();

        $totalAssets      = 0.0;
        $totalLiabilities = 0.0;
        $totalEquity      = 0.0;

        foreach ($rows as $row) {
            $amount = (float) $row['amount'];
            $class  = (int) $row['account_class'];

            if ($class === 1) {
                $totalAssets += $amount;
            } elseif ($class === 2) {
                $totalLiabilities += $amount;
            } elseif ($class === 3) {
                $totalEquity += $amount;
            }
        }

        $equationOk = (round($totalAssets, 2) === round($totalLiabilities + $totalEquity, 2));

        $reportData = [
            'type'             => 'balance_sheet',
            'title'            => 'Balance General',
            'period_start'     => $asOfDate,
            'period_end'       => $asOfDate,
            'lines'            => $rows,
            'total_assets'     => $totalAssets,
            'total_liabilities'=> $totalLiabilities,
            'total_equity'     => $totalEquity,
            'equation_ok'      => $equationOk,
        ];

        $json = json_encode($reportData, JSON_UNESCAPED_UNICODE);
        $hash = hash('sha256', $json);

        $this->markPreviousModifiedIfChanged('balance_sheet', $asOfDate, $asOfDate, $hash);

        $insertSql = "INSERT INTO reports 
                        (report_type, period_start, period_end, report_data, hash, generated_by)
                      VALUES 
                        ('balance_sheet', :start, :end, :data, :hash, :user_id)";

        $stmtInsert = $this->db->prepare($insertSql);
        $stmtInsert->execute([
            'start'   => $asOfDate,
            'end'     => $asOfDate,
            'data'    => $json,
            'hash'    => $hash,
            'user_id' => $userId,
        ]);

        $reportId = (int) $this->db->lastInsertId();
        $report   = $this->getReportById($reportId);

        return [
            'data'   => $reportData,
            'report' => $report,
        ];
    }

    /* ========================
     *  FIRMA
     * ======================== */

    public function signReport(int $reportId, int $userId): bool
    {
        $report = $this->getReportById($reportId);
        if (!$report) {
            return false;
        }

        if (!empty($report['is_signed']) && (int)$report['is_signed'] === 1) {
            // Ya está firmado
            return true;
        }

        $snapshot      = $report['report_data']; // JSON tal cual
        $signatureHash = hash('sha256', $report['hash'] . '|' . $userId . '|' . microtime(true));

        try {
            $this->db->beginTransaction();

            $sqlIns = "INSERT INTO report_signatures (report_id, user_id, signature_hash, original_snapshot)
                       VALUES (:report_id, :user_id, :signature_hash, :snapshot)";

            $stmtIns = $this->db->prepare($sqlIns);
            $stmtIns->execute([
                'report_id'      => $reportId,
                'user_id'        => $userId,
                'signature_hash' => $signatureHash,
                'snapshot'       => $snapshot,
            ]);

            $sqlUpd = "UPDATE reports SET is_signed = 1 WHERE id = :id";
            $stmtUpd = $this->db->prepare($sqlUpd);
            $stmtUpd->execute(['id' => $reportId]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /* ========================
     *  UTILIDAD: MARCAR MODIFICADOS
     * ======================== */

    private function markPreviousModifiedIfChanged(string $type, string $start, string $end, string $newHash): void
    {
        $sql = "SELECT id, hash, is_signed
                FROM reports
                WHERE report_type = :type
                  AND period_start = :start
                  AND period_end   = :end";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'type'  => $type,
            'start' => $start,
            'end'   => $end,
        ]);

        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            if ($row['hash'] !== $newHash && (int)$row['is_signed'] === 1) {
                $upd = $this->db->prepare("UPDATE reports SET is_modified = 1 WHERE id = :id");
                $upd->execute(['id' => $row['id']]);
            }
        }
    }
}
