<?php
// views/accounts/list.php
include BASE_PATH . '/views/layouts/header.php';

$classLabels = [
    1 => 'Activo',
    2 => 'Pasivo',
    3 => 'Patrimonio',
    4 => 'Ingresos',
    5 => 'Gastos',
    6 => 'Costos',
    7 => 'Otros Gastos',
];
?>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'created')   $text = 'Cuenta creada correctamente.';
    if ($msg === 'updated')   $text = 'Cuenta actualizada correctamente.';
    if ($msg === 'status')    $text = 'Estado de la cuenta actualizado.';
    if ($msg === 'notfound')  $text = 'Cuenta no encontrada.';
    if ($msg === 'noperm')    $text = 'No tiene permisos para modificar el catálogo de cuentas.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<section class="page-header">
    <div>
        <h1 class="page-header-title">Catálogo de Cuentas</h1>
        <p class="page-header-subtitle">
            Consulta el catálogo de cuentas contables. Usa el buscador para filtrar.
        </p>
    </div>

    <?php if (empty($isAdminLimited)): ?>
            <a href="<?= BASE_URL ?>/accounts.php?action=create"
               class="btn btn-primary btn-small">
                Nueva cuenta
            </a>
        <?php else: ?>
            <button type="button"
                    class="btn btn-secondary btn-small"
                    style="pointer-events:none; opacity:.6;"
                    title="El administrador solo tiene acceso de lectura al catálogo de cuentas.">
                Nueva cuenta
            </button>
        <?php endif; ?>

    <div class="page-toolbar">
        <form class="search-form" method="get" action="<?= BASE_URL ?>/accounts.php">
            <input type="hidden" name="action" value="list">
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
                    placeholder="Buscar cuenta..."
                    value="<?= htmlspecialchars($currentSearch ?? '') ?>"
                >
                <button type="submit" class="btn btn-primary btn-small btn-search">
                    Buscar
                </button>
            </div>
        </form>

        
    </div>
</section>

<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Clase</th>
            <th>Tipo</th>
            <th>Balance</th>
            <th>Activo</th>
            <th>Creada por</th>
            <th>Fecha creación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($accounts)): ?>
        <tr><td colspan="9">No hay cuentas registradas.</td></tr>
    <?php else: ?>
        <?php foreach ($accounts as $acc): ?>
            <tr>
                <td><?= htmlspecialchars($acc['code']) ?></td>
                <td><?= htmlspecialchars($acc['name']) ?></td>
                <td><?= $classLabels[$acc['account_class']] ?? $acc['account_class'] ?></td>
                <td><?= $acc['account_type'] === 'debit' ? 'Débito' : 'Crédito' ?></td>
                <td><?= number_format((float)$acc['balance'], 2) ?></td>
                <td><?= $acc['is_active'] ? 'Sí' : 'No' ?></td>
                <td><?= htmlspecialchars($acc['created_by_username'] ?? '-') ?></td>
                <td><?= htmlspecialchars($acc['created_at']) ?></td>
                <td class="table-actions">
                    <?php if (empty($isAdminLimited)): ?>
                        <a href="<?= BASE_URL ?>/accounts.php?action=edit&id=<?= (int)$acc['id'] ?>"
                           class="btn btn-secondary btn-small">
                            Editar
                        </a>
                        <a href="<?= BASE_URL ?>/accounts.php?action=toggle&id=<?= (int)$acc['id'] ?>"
                           class="btn btn-secondary btn-small">
                            <?= $acc['is_active'] ? 'Desactivar' : 'Activar' ?>
                        </a>
                    <?php else: ?>
                        <button type="button"
                                class="btn btn-secondary btn-small"
                                style="pointer-events:none; opacity:.6;"
                                title="Acciones solo disponibles para usuarios contables autorizados.">
                            Solo lectura
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
