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

<h1>Catálogo de Cuentas</h1>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'created')   $text = 'Cuenta creada correctamente.';
    if ($msg === 'updated')   $text = 'Cuenta actualizada correctamente.';
    if ($msg === 'status')    $text = 'Estado de la cuenta actualizado.';
    if ($msg === 'notfound')  $text = 'Cuenta no encontrada.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<a href="<?= BASE_URL ?>/accounts.php?action=create" class="btn btn-primary" style="margin-bottom:1rem;">Nueva cuenta</a>

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
                <td>
                    <a href="<?= BASE_URL ?>/accounts.php?action=edit&id=<?= (int)$acc['id'] ?>">Editar</a> |
                    <a href="<?= BASE_URL ?>/accounts.php?action=toggle&id=<?= (int)$acc['id'] ?>">
                        <?= $acc['is_active'] ? 'Desactivar' : 'Activar' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
