<?php
// views/transactions/list.php
include BASE_PATH . '/views/layouts/header.php';
?>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'created')    $text = 'Transacción creada correctamente.';
    if ($msg === 'deleted')    $text = 'Transacción eliminada.';
    if ($msg === 'notdeleted') $text = 'No se pudo eliminar (probablemente ya está posteada).';
    if ($msg === 'notfound')   $text = 'Transacción no encontrada.';
    if ($msg === 'noperm')     $text = 'No tiene permisos para crear o modificar transacciones.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<section class="page-header">
    <div>
        <h1 class="page-header-title">Diario General</h1>
        <p class="page-header-subtitle">
            Consulta las transacciones registradas. Puedes buscar por descripción, fecha, usuario o cualquier dato relacionado.
        </p>
    </div>

    <?php if (empty($isAdminLimited)): ?>
            <a href="<?= BASE_URL ?>/transactions.php?action=create"
               class="btn btn-primary btn-small">
                Nueva transacción
            </a>
        <?php else: ?>
            <button type="button"
                    class="btn btn-secondary btn-small"
                    style="pointer-events:none; opacity:.6;"
                    title="El administrador solo puede consultar el historial, no crear transacciones.">
                Nueva transacción
            </button>
        <?php endif; ?>

    <div class="page-toolbar">
        <form class="search-form" method="get" action="<?= BASE_URL ?>/transactions.php">
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
                    placeholder="Buscar transacción..."
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
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Débitos</th>
            <th>Créditos</th>
            <th>Usuario</th>
            <th>Posteada</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($transactions)): ?>
        <tr><td colspan="7">No hay transacciones registradas.</td></tr>
    <?php else: ?>
        <?php foreach ($transactions as $tx): ?>
            <tr>
                <td><?= htmlspecialchars($tx['transaction_date']) ?></td>
                <td><?= htmlspecialchars($tx['description']) ?></td>
                <td><?= number_format((float)$tx['total_debit'], 2) ?></td>
                <td><?= number_format((float)$tx['total_credit'], 2) ?></td>
                <td><?= htmlspecialchars($tx['created_by_username'] ?? '-') ?></td>
                <td>
                    <?php if ($tx['is_posted']): ?>
                        <span class="badge badge-success">Sí</span>
                    <?php else: ?>
                        <span class="badge badge-warning">No</span>
                    <?php endif; ?>
                </td>
                <td class="table-actions">
                    <a href="<?= BASE_URL ?>/transactions.php?action=view&id=<?= (int)$tx['id'] ?>"
                       class="btn btn-secondary btn-small">
                        Ver
                    </a>

                    <?php if (empty($isAdminLimited) && !$tx['is_posted']): ?>
                        <a href="<?= BASE_URL ?>/transactions.php?action=delete&id=<?= (int)$tx['id'] ?>"
                           class="btn btn-secondary btn-small"
                           onclick="return confirm('¿Eliminar esta transacción?');">
                            Eliminar
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
