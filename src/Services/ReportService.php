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

        // === NUEVO: serializamos una sola vez y lo guardamos tanto en report_data como en data_json ===
        $json = json_encode($reportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = hash('sha256', $json);

        $this->markPreviousModifiedIfChanged('income_statement', $periodStart, $periodEnd, $hash);

        $insertSql = "INSERT INTO reports 
                        (report_type, period_start, period_end, report_data, data_json, hash, generated_by)
                      VALUES 
                        ('income_statement', :start, :end, :data, :data_json, :hash, :user_id)";

        $stmtInsert = $this->db->prepare($insertSql);
        $stmtInsert->execute([
            'start'      => $periodStart,
            'end'        => $periodEnd,
            'data'       => $json,   // se mantiene para compatibilidad (report_data)
            'data_json'  => $json,   // NUEVO: estructura para reconstruir tabla en el PDF
            'hash'       => $hash,
            'user_id'    => $userId,
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
            'type'              => 'balance_sheet',
            'title'             => 'Balance General',
            'period_start'      => $asOfDate,
            'period_end'        => $asOfDate,
            'lines'             => $rows,
            'total_assets'      => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity'      => $totalEquity,
            'equation_ok'       => $equationOk,
        ];

        // === NUEVO: serializamos y guardamos también en data_json ===
        $json = json_encode($reportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = hash('sha256', $json);

        $this->markPreviousModifiedIfChanged('balance_sheet', $asOfDate, $asOfDate, $hash);

        $insertSql = "INSERT INTO reports 
                        (report_type, period_start, period_end, report_data, data_json, hash, generated_by)
                      VALUES 
                        ('balance_sheet', :start, :end, :data, :data_json, :hash, :user_id)";

        $stmtInsert = $this->db->prepare($insertSql);
        $stmtInsert->execute([
            'start'      => $asOfDate,
            'end'        => $asOfDate,
            'data'       => $json,   // compatibilidad con report_data
            'data_json'  => $json,   // NUEVO: estructura para el PDF
            'hash'       => $hash,
            'user_id'    => $userId,
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

    /**
     * Firma un reporte y devuelve el hash de firma en $hashOut.
     *
     * Usa la tabla report_signatures (report_id, user_id, signature_hash, original_snapshot)
     * y marca el reporte como firmado en reports.is_signed.
     */
    public function signReport(int $reportId, int $userId, ?string &$hashOut = null): bool
    {
        // 1) Cargar el reporte
        $report = $this->getReportById($reportId);
        if (!$report) {
            return false;
        }

        // Si ya está marcado como firmado, no volvemos a firmar
        if (!empty($report['is_signed']) && (int)$report['is_signed'] === 1) {
            // Opcionalmente devolver la última firma si la tienes en el join
            $hashOut = $report['signature_hash'] ?? null;
            return true;
        }

        // 2) Construir payload lógico para la firma (contenido del informe)
        $payload = [
            'id'           => $report['id'],
            'report_type'  => $report['report_type'],
            'period_start' => $report['period_start'] ?? null,
            'period_end'   => $report['period_end']   ?? null,
        ];

        // Tomar la estructura que guardamos en JSON (data_json o report_data)
        $dataJson = $report['data_json'] ?? $report['report_data'] ?? null;
        if (is_string($dataJson)) {
            $decoded = json_decode($dataJson, true);
            if (is_array($decoded)) {
                $payload['data'] = $decoded;
            }
        }

        $jsonPayload    = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $signatureHash  = hash('sha256', $jsonPayload);

        // 3) Construir el PDF canónico y calcular hash del PDF
        //    (usa el mismo método que luego usará downloadSigned)
        $pdfBinary = $this->buildPdfForReport($report);
        $pdfHash   = hash('sha256', $pdfBinary);
        $hashAlgo  = 'sha256';

        try {
            $this->db->beginTransaction();

            // 4) Registrar la firma en la tabla report_signatures (histórico)
            $sqlIns = "INSERT INTO report_signatures (report_id, user_id, signature_hash, original_snapshot)
                    VALUES (:report_id, :user_id, :signature_hash, :snapshot)";

            $stmtIns = $this->db->prepare($sqlIns);
            $stmtIns->execute([
                'report_id'      => $reportId,
                'user_id'        => $userId,
                'signature_hash' => $signatureHash,
                'snapshot'       => $jsonPayload,
            ]);

            // 5) Marcar el reporte como firmado y guardar hash del PDF + algoritmo
            //    OJO: aquí SOLO usamos columnas que sí existen en la tabla reports:
            //         is_signed, is_modified, pdf_hash, hash_algo
            $sqlUpd = "UPDATE reports
                    SET is_signed   = 1,
                        is_modified = 0,
                        pdf_hash    = :pdf_hash,
                        hash_algo   = :hash_algo
                    WHERE id = :id";

            $stmtUpd = $this->db->prepare($sqlUpd);
            $stmtUpd->execute([
                'pdf_hash'  => $pdfHash,
                'hash_algo' => $hashAlgo,
                'id'        => $reportId,
            ]);

            $this->db->commit();

            // Devolvemos el hash lógico de firma para mostrarlo UNA sola vez en la pantalla emergente
            $hashOut = $signatureHash;
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            // Aquí puedes loguear $e->getMessage() si quieres
            return false;
        }
    }


    /**
     * Devuelve el binario del PDF para un reporte firmado.
     * Se usa para descargar y para calcular el hash.
     */
    public function getReportPdfBinary(int $id): ?string
    {
        $report = $this->getReportById($id);
        if (!$report || (int)($report['is_signed'] ?? 0) !== 1) {
            return null;
        }

        return $this->buildPdfForReport($report);
    }

    /**
     * Genera el contenido PDF (binario) para un reporte.
     * Usa data_json o report_data para construir la tabla.
     */
    private function buildPdfForReport(array $report): string
    {
        // Cargar datos JSON del reporte
        $dataJson = $report['data_json'] ?? $report['report_data'] ?? null;
        $data     = [];

        if (is_string($dataJson)) {
            $decoded = json_decode($dataJson, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // Definimos ruta de fuentes para FPDF
        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', BASE_PATH . '/src/Lib/fpdf/font/');
        }

        require_once BASE_PATH . '/src/Lib/fpdf/fpdf.php';

        // Helper para codificar a ISO-8859-1 sin utf8_decode()
        $toLatin1 = static function (string $text): string {
            if (!function_exists('mb_convert_encoding')) {
                return $text;
            }
            return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        };

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $toLatin1('Reporte financiero'), 0, 1, 'C');
        $pdf->Ln(5);

        // Encabezado principal
        $typeLabel = 'Reporte';
        if (($report['report_type'] ?? '') === 'income_statement') {
            $typeLabel = 'Estado de Resultados';
        } elseif (($report['report_type'] ?? '') === 'balance_sheet') {
            $typeLabel = 'Balance General';
        }

        $period = (string)($report['period_start'] ?? '');
        if (!empty($report['period_end']) && $report['period_end'] !== $report['period_start']) {
            $period .= ' al ' . $report['period_end'];
        }

        $generatedBy = (string)($report['generated_by_username'] ?? 'N/D');
        $generatedAt = (string)($report['generated_at'] ?? '');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, $toLatin1('Tipo: ' . $typeLabel), 0, 1);
        $pdf->Cell(0, 8, $toLatin1('Período: ' . $period), 0, 1);
        $pdf->Cell(0, 8, $toLatin1('Generado por: ' . $generatedBy), 0, 1);
        $pdf->Cell(0, 8, $toLatin1('Fecha generación: ' . $generatedAt), 0, 1);
        $pdf->Ln(5);

        // Título de detalle
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, $toLatin1('Detalle del informe'), 0, 1);
        $pdf->Ln(3);

        // Tabla principal
        if (!empty($data['lines']) && is_array($data['lines'])) {
            // Encabezados
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(40, 8, $toLatin1('Código'), 1, 0);
            $pdf->Cell(100, 8, $toLatin1('Cuenta'), 1, 0);
            $pdf->Cell(50, 8, $toLatin1('Monto'), 1, 1, 'R');

            // Filas
            $pdf->SetFont('Arial', '', 10);
            foreach ($data['lines'] as $line) {
                $code   = (string)($line['code'] ?? '');
                $name   = (string)($line['name'] ?? '');
                $amount = (float)($line['amount'] ?? 0);

                $pdf->Cell(40, 6, $toLatin1($code), 1, 0);
                $pdf->Cell(100, 6, $toLatin1($name), 1, 0);
                $pdf->Cell(50, 6, number_format($amount, 2), 1, 1, 'R');
            }

            // Totales según tipo
            $type = $data['type'] ?? '';

            if ($type === 'income_statement') {
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 11);

                $pdf->Cell(140, 6, $toLatin1('Total Ingresos'), 0, 0, 'R');
                $pdf->Cell(50, 6, number_format((float)($data['total_income'] ?? 0), 2), 0, 1, 'R');

                $pdf->Cell(140, 6, $toLatin1('Total Gastos'), 0, 0, 'R');
                $pdf->Cell(50, 6, number_format((float)($data['total_expense'] ?? 0), 2), 0, 1, 'R');

                $pdf->Cell(140, 6, $toLatin1('Utilidad Neta'), 0, 0, 'R');
                $pdf->Cell(50, 6, number_format((float)($data['net_income'] ?? 0), 2), 0, 1, 'R');
            } elseif ($type === 'balance_sheet') {
                $pdf->Ln(4);
                $pdf->SetFont('Arial', 'B', 11);

                $pdf->Cell(140, 6, $toLatin1('Total Activos'), 0, 0, 'R');
                $pdf->Cell(50, 6, number_format((float)($data['total_assets'] ?? 0), 2), 0, 1, 'R');

                $pdf->Cell(140, 6, $toLatin1('Total Pasivos'), 0, 0, 'R');
                $pdf->Cell(50, 6, number_format((float)($data['total_liabilities'] ?? 0), 2), 0, 1, 'R');

                $pdf->Cell(140, 6, $toLatin1('Total Patrimonio'), 0, 0, 'R');
                $pdf->Cell(50, 6, number_format((float)($data['total_equity'] ?? 0), 2), 0, 1, 'R');

                $pdf->Ln(3);
                $eqText = !empty($data['equation_ok'])
                    ? 'La ecuación contable cuadra (Activos = Pasivos + Patrimonio).'
                    : 'Advertencia: la ecuación contable NO cuadra.';
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, $toLatin1($eqText));
            }
        } else {
            $pdf->SetFont('Arial', '', 11);
            $pdf->MultiCell(0, 6, $toLatin1('No hay líneas de detalle registradas para este informe.'));
        }

        // Devolvemos el binario del PDF (no lo enviamos al navegador aquí)
        return $pdf->Output('S');
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
