<?php
// views/reports/sign.php
include BASE_PATH . '/views/layouts/header.php';

$error  = $error  ?? null;
$report = $report ?? null;

if ($report) {
    $data = json_decode($report['report_data'], true);
}
?>

<h1>Firmar Informe</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <a href="<?= BASE_URL ?>/reports.php" class="btn btn-secondary">Volver</a>
<?php elseif ($report && $data): ?>
    <div class="card">
        <p><strong>Tipo:</strong>
            <?= $report['report_type'] === 'income_statement' ? 'Estado de Resultados' : 'Balance General' ?>
        </p>
        <p><strong>Período:</strong>
            <?= htmlspecialchars($report['period_start']) ?>
            <?php if ($report['period_start'] !== $report['period_end']): ?>
                al <?= htmlspecialchars($report['period_end']) ?>
            <?php endif; ?>
        </p>
        <p><strong>Generado por:</strong> <?= htmlspecialchars($report['generated_by_username'] ?? '-') ?></p>
        <p><strong>Fecha generación:</strong> <?= htmlspecialchars($report['generated_at']) ?></p>
        <?php if (!empty($report['is_signed'])): ?>
            <p><strong>Estado actual:</strong> Ya está firmado.</p>
        <?php endif; ?>
        <?php if (!empty($report['is_modified'])): ?>
            <p><strong>Aviso:</strong> ⚠️ El informe fue marcado como modificado después de la firma.</p>
        <?php endif; ?>
    </div>

    <p class="small-note">
        Al firmar el informe declaras que los datos son veraces para el período indicado.
        Si posteriormente se cambia la información contable y se genera un nuevo informe
        del mismo período, este quedará marcado como <strong>“Modificado”</strong>.
    </p>

    <?php if (empty($report['is_signed']) && !$error): ?>
        <form action="<?= BASE_URL ?>/reports.php?action=sign&id=<?= (int)$report['id'] ?>" method="post">
            <button type="submit" class="btn btn-primary">Firmar informe</button>
            <a href="<?= BASE_URL ?>/reports.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php else: ?>
        <a href="<?= BASE_URL ?>/reports.php" class="btn btn-secondary">Volver</a>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-error">Informe no encontrado.</div>
    <a href="<?= BASE_URL ?>/reports.php" class="btn btn-secondary">Volver</a>
<?php endif; ?>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
