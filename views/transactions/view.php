<?php
// views/transactions/view.php
include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Detalle de Transacción</h1>

<section class="card">
    <p><strong>ID:</strong> <?= (int)$transaction['id'] ?></p>
    <p><strong>Fecha:</strong> <?= htmlspecialchars($transaction['transaction_date']) ?></p>
    <p><strong>Descripción:</strong> <?= htmlspecialchars($transaction['description']) ?></p>
    <p><strong>Usuario que registra:</strong> <?= htmlspecialchars($transaction['created_by_username'] ?? '-') ?></p>
    <p><strong>Posteada:</strong> <?= $transaction['is_posted'] ? 'Sí' : 'No' ?></p>
</section>

<h3>Líneas</h3>
<table class="table">
    <thead>
        <tr>
            <th>Cuenta</th>
            <th>Nombre</th>
            <th>Débito</th>
            <th>Crédito</th>
            <th>Memo</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($lines)): ?>
        <tr><td colspan="5">No hay líneas asociadas.</td></tr>
    <?php else: ?>
        <?php foreach ($lines as $line): ?>
            <tr>
                <td><?= htmlspecialchars($line['code']) ?></td>
                <td><?= htmlspecialchars($line['name']) ?></td>
                <td><?= number_format((float)$line['debit'], 2) ?></td>
                <td><?= number_format((float)$line['credit'], 2) ?></td>
                <td><?= htmlspecialchars($line['memo'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<a href="<?= BASE_URL ?>/transactions.php" class="btn btn-secondary">Volver</a>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
