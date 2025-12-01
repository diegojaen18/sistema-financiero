<?php
// views/transactions/list.php
include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Diario General</h1>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'created')    $text = 'Transacción creada correctamente.';
    if ($msg === 'deleted')    $text = 'Transacción eliminada.';
    if ($msg === 'notdeleted') $text = 'No se pudo eliminar (probablemente ya está posteada).';
    if ($msg === 'notfound')   $text = 'Transacción no encontrada.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<a href="<?= BASE_URL ?>/transactions.php?action=create" class="btn btn-primary" style="margin-bottom:1rem;">Nueva transacción</a>

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
                <td>
                    <a href="<?= BASE_URL ?>/transactions.php?action=view&id=<?= (int)$tx['id'] ?>">Ver</a>
                    <?php if (!$tx['is_posted']): ?>
                        | <a href="<?= BASE_URL ?>/transactions.php?action=delete&id=<?= (int)$tx['id'] ?>"
                             onclick="return confirm('¿Eliminar esta transacción?');">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
