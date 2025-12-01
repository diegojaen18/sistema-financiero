<?php
// views/reports/balance_sheet.php
include BASE_PATH . '/views/layouts/header.php';

$errors     = $errors     ?? [];
$reportData = $reportData ?? null;

$today = date('Y-m-d');
?>

<h1>Balance General</h1>
<p class="small-note">
    Muestra el saldo de Activos, Pasivos y Patrimonio en una fecha determinada.
</p>

<form action="<?= BASE_URL ?>/reports.php?action=balance" method="post" class="form">
    <div class="form-group">
        <label for="as_of_date">Fecha de corte</label>
        <input type="date" name="as_of_date" id="as_of_date"
               value="<?= htmlspecialchars($_POST['as_of_date'] ?? $today) ?>" required>
        <?php if (!empty($errors['as_of_date'])): ?>
            <small class="error"><?= htmlspecialchars($errors['as_of_date']) ?></small>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Generar informe</button>
    <a href="<?= BASE_URL ?>/reports.php" class="btn btn-secondary">Volver</a>
</form>

<?php if ($reportData): ?>
    <h2 class="mt-3">Detalle de cuentas</h2>
    <?php
        $classLabels = [
            1 => 'Activos',
            2 => 'Pasivos',
            3 => 'Patrimonio',
        ];
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Cuenta</th>
                <th>Clase</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
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
        <p><strong>Total Activos:</strong> <?= number_format($reportData['total_assets'], 2) ?></p>
        <p><strong>Total Pasivos:</strong> <?= number_format($reportData['total_liabilities'], 2) ?></p>
        <p><strong>Total Patrimonio:</strong> <?= number_format($reportData['total_equity'], 2) ?></p>
        <p>
            <strong>Ecuación contable:</strong>
            Activo = Pasivo + Patrimonio →
            <?= number_format($reportData['total_assets'], 2) ?>
            =
            <?= number_format($reportData['total_liabilities'] + $reportData['total_equity'], 2) ?>
            <?php if ($reportData['equation_ok']): ?>
                <span class="badge badge-success">Correcto</span>
            <?php else: ?>
                <span class="badge badge-warning">No cuadra</span>
            <?php endif; ?>
        </p>
    </div>
<?php endif; ?>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
