<?php
// views/reports/validate.php

include BASE_PATH . '/views/layouts/header.php';

/**
 * Variables que esperamos:
 * - $reports    (array de reportes)
 * - $errors     (array de errores)
 * - $resultMsg  (string|null)
 * - $isValid    (bool|null)
 */

$errors    = $errors    ?? [];
$resultMsg = $resultMsg ?? null;
$isValid   = $isValid   ?? null;
?>

<h1>Validar Reporte Financiero</h1>

<p class="page-header-subtitle">
    Selecciona un reporte previamente firmado y adjunta el PDF que deseas validar.
    El sistema comparará la firma hash almacenada con el hash del archivo.
</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $field => $msg): ?>
                <li><?= htmlspecialchars($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form
    class="form"
    method="post"
    action="<?= BASE_URL ?>/reports.php?action=validate_post"
    enctype="multipart/form-data"
    style="margin-top:1rem; max-width:500px;"
>
    <div class="form-group">
        <label for="report_id">Reporte a validar:</label>
        <select name="report_id" id="report_id" class="form-control">
            <option value="">-- Seleccione un reporte --</option>
            <?php foreach ($reports as $r): ?>
                <?php
                    $labelType = $r['report_type'] === 'income_statement'
                        ? 'Estado de Resultados'
                        : 'Balance General';

                    $period = $r['period_start'];
                    if ($r['period_start'] !== $r['period_end']) {
                        $period .= ' al ' . $r['period_end'];
                    }

                    $optionText = sprintf(
                        '%s (%s) - generado el %s',
                        $labelType,
                        $period,
                        $r['generated_at']
                    );
                ?>
                <option value="<?= (int)$r['id'] ?>">
                    <?= htmlspecialchars($optionText) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group" style="margin-top:1rem;">
        <label for="report_file">Archivo PDF del reporte:</label>
        <input type="file" name="report_file" id="report_file" class="form-control" accept="application/pdf">
    </div>

    <button type="submit" class="btn btn-primary" style="margin-top:1rem;">
        Validar reporte
    </button>
</form>

<?php
// Disparamos la ventana emergente si hay resultado de validación
if ($resultMsg !== null && $isValid !== null): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var mensaje = <?= json_encode($resultMsg, JSON_UNESCAPED_UNICODE) ?>;
            var esValido = <?= $isValid ? 'true' : 'false' ?>;

            // Si tienes AppModal definido (como en reports/list.php), lo usamos:
            if (window.AppModal && typeof window.AppModal.show === 'function') {
                var titulo = esValido
                    ? 'Validación exitosa'
                    : 'Validación fallida';

                // En el tercer parámetro puedes pasar un texto extra o dejarlo vacío
                window.AppModal.show(titulo, mensaje, '');
            } else {
                // Fallback: alert nativo
                alert(mensaje);
            }
        });
    </script>
<?php endif; ?>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
