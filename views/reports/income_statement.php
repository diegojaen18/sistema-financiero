<?php
// views/reports/income_statement.php
include BASE_PATH . '/views/layouts/header.php';

$errors     = $errors     ?? [];
$reportData = $reportData ?? null;

$today      = date('Y-m-d');
$monthStart = date('Y-m-01');
?>

<h1>Estado de Resultados</h1>
<p class="small-note">
    Resume los Ingresos y Gastos para calcular la utilidad neta durante un período específico.
</p>

<form action="<?= BASE_URL ?>/reports.php?action=income" method="post" class="form">
    <div class="form-group">
        <label for="period_start">Fecha inicio</label>
        <input type="date" name="period_start" id="period_start"
               value="<?= htmlspecialchars($_POST['period_start'] ?? $monthStart) ?>" required>
        <?php if (!empty($errors['period_start'])): ?>
            <small class="error"><?= htmlspecialchars($errors['period_start']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="period_end">Fecha fin</label>
        <input type="date" name="period_end" id="period_end"
               value="<?= htmlspecialchars($_POST['period_end'] ?? $today) ?>" required>
        <?php if (!empty($errors['period_end'])): ?>
            <small class="error"><?= htmlspecialchars($errors['period_end']) ?></small>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Generar informe</button>
    <a href="<?= BASE_URL ?>/reports.php" class="btn btn-secondary">Volver</a>
</form>

<?php if ($reportData): ?>
    <h2 class="mt-3">Resultados del período</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Cuenta</th>
                <th>Clase</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $classLabels = [
                4 => 'Ingresos',
                5 => 'Gastos',
                6 => 'Costos',
                7 => 'Otros Gastos',
            ];
        ?>
        <?php foreach ($reportData['lines'] as $line): ?>
            <tr>
                <td><?= htmlspecialchars($line['code']) ?></td>
                <td><?= htmlspecialchars($line['name']) ?></td>
                <td><?= $classLabels[(int)$line['account_class']] ?? $line['account_class'] ?></td>
                <td><?= number_format((float)$line['amount'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="card">
        <p><strong>Total Ingresos:</strong> <?= number_format($reportData['total_income'], 2) ?></p>
        <p><strong>Total Gastos/Costos:</strong> <?= number_format($reportData['total_expense'], 2) ?></p>
        <p><strong>Utilidad neta del período:</strong> <?= number_format($reportData['net_income'], 2) ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($reportGenerated)): ?>
    <script>
        alert('Informe de Estado de Resultados generado correctamente.');
    </script>
<?php endif; ?>


<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
