<?php
// views/reports/list.php
include BASE_PATH . '/views/layouts/header.php';

function report_type_label(string $type): string {
    return $type === 'income_statement'
        ? 'Estado de Resultados'
        : 'Balance General';
}
?>

<h1>Informes Financieros</h1>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'signed')    $text = 'Informe firmado correctamente.';
    if ($msg === 'signerror') $text = 'No se pudo firmar el informe.';
    if ($msg === 'noperm')    $text = 'No tiene permisos para firmar informes.';
    if ($msg === 'notfound')  $text = 'Informe no encontrado.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<section class="dashboard-grid" style="margin-top:1rem;">
    <a href="<?= BASE_URL ?>/reports.php?action=income" class="dashboard-card">
        <div class="dashboard-card-icon">📈</div>
        <h2>Estado de Resultados</h2>
        <p>Resume ingresos y gastos en un período.</p>
    </a>

    <a href="<?= BASE_URL ?>/reports.php?action=balance" class="dashboard-card">
        <div class="dashboard-card-icon">📊</div>
        <h2>Balance General</h2>
        <p>Muestra activos, pasivos y patrimonio.</p>
    </a>
</section>

<h2 class="mt-3">Historial de informes generados</h2>

<table class="table">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Período</th>
            <th>Generado por</th>
            <th>Fecha generación</th>
            <th>Estado de firma</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($reports)): ?>
        <tr><td colspan="6">Todavía no se han generado informes.</td></tr>
    <?php else: ?>
        <?php foreach ($reports as $r): ?>
            <?php
                $period = $r['period_start'];
                if ($r['period_start'] !== $r['period_end']) {
                    $period .= " al " . $r['period_end'];
                }

                $isSigned   = !empty($r['is_signed']);
                $isModified = !empty($r['is_modified']);
            ?>
            <tr>
                <td><?= htmlspecialchars(report_type_label($r['report_type'])) ?></td>
                <td><?= htmlspecialchars($period) ?></td>
                <td><?= htmlspecialchars($r['generated_by_username'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['generated_at']) ?></td>
                <td>
                    <?php if ($isSigned && !$isModified): ?>
                        <span class="badge badge-success">✅ Firmado</span>
                    <?php elseif ($isSigned && $isModified): ?>
                        <span class="badge badge-warning">⚠️ Modificado</span>
                    <?php else: ?>
                        <span class="badge">Pendiente</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($canSign) && !$isSigned): ?>
                        <a href="<?= BASE_URL ?>/reports.php?action=sign&id=<?= (int)$r['id'] ?>">Firmar</a>
                    <?php else: ?>
                        <!-- Podrías agregar "Ver" más adelante si quieres mostrar el JSON -->
                        <span class="small-note">Sin acciones</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
