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

<?php
// -----------------------------
// Bloque de mensajes simples
// -----------------------------
$msg  = $_GET['msg'] ?? '';
$text = '';

if ($msg === 'signed')    $text = 'Informe firmado correctamente.';
if ($msg === 'signerror') $text = 'No se pudo firmar el informe.';
if ($msg === 'noperm')    $text = 'No tiene permisos para ejecutar esa acción sobre informes.';
if ($msg === 'notfound')  $text = 'Informe no encontrado.';

if ($text): ?>
    <div class="alert"><?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php
// -----------------------------
// Datos para el modal de hash y la descarga automática del PDF
// -----------------------------
$signatureHash = $_GET['hash'] ?? '';
$downloadId    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var msg = '<?= htmlspecialchars($msg, ENT_QUOTES, "UTF-8") ?>';
        var hash = '<?= htmlspecialchars($signatureHash, ENT_QUOTES, "UTF-8") ?>';
        var downloadId = <?= $downloadId ?>;

        // Si venimos de firmar
        if (msg === 'signed') {
            // 1) Disparar descarga del PDF firmado en una nueva pestaña
            if (downloadId) {
                // Llama a reports.php?action=download&id=...
                window.open('<?= BASE_URL ?>/reports.php?action=download&id=' + downloadId, '_blank');
            }

            // 2) Mostrar la pantalla emergente con el hash una sola vez
            if (hash && window.AppModal && typeof window.AppModal.show === 'function') {
                window.AppModal.show(
                    'Informe firmado correctamente',
                    'Este es el hash único de la firma de este informe. ' +
                    'Guárdalo en un lugar seguro. Esta es la única vez que se mostrará en pantalla.',
                    hash
                );
            }
        }
    });
</script>

<?php
// Si la variable no existe, la normalizamos:
$isAuditor = !empty($isAuditor);
?>

<?php if (!$isAuditor): ?>
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
<?php else: ?>
    <section style="margin-top:1rem;">
        <p class="page-header-subtitle">
            Como Auditor, tienes acceso de solo lectura al historial de informes generados.
            No puedes crear ni firmar nuevos informes.
        </p>
    </section>
<?php endif; ?>

<h2 class="mt-3">Historial de informes generados</h2>

<p class="page-header-subtitle">
    Revisa los informes generados (estados de resultado, balances, etc.). Filtra por tipo de informe o período.
</p>

<form class="search-form" method="get" action="<?= BASE_URL ?>/reports.php">
    <input type="hidden" name="action" value="index">
    <div class="search-group">
        <span class="search-icon" aria-hidden="true">
            <svg viewBox="0 0 20 20">
                <circle cx="8.5" cy="8.5" r="5.5"></circle>
                <line x1="12" y1="12" x2="17" y2="17"></line>
            </svg>
        </span>
        <input
            type="text"
            name="search"
            class="search-input"
            placeholder="Buscar informe..."
            value="<?= htmlspecialchars($currentSearch ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >
        <button type="submit" class="btn btn-primary btn-small btn-search">
            Buscar
        </button>
    </div>
</form>

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
                <td><?= htmlspecialchars(report_type_label($r['report_type']), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($period, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['generated_by_username'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['generated_at'], ENT_QUOTES, 'UTF-8') ?></td>
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
                    <?php if ($isAuditor): ?>
                        <span class="small-note">Solo lectura</span>
                    <?php elseif (!empty($canSign) && !$isSigned): ?>
                        <a href="<?= BASE_URL ?>/reports.php?action=sign&id=<?= (int)$r['id'] ?>">Firmar</a>
                    <?php else: ?>
                        <span class="small-note">Sin acciones</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
