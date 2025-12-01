<?php
// views/transactions/create.php

use App\Security\Sanitizer;

$errors  = $errors  ?? [];
$oldData = $oldData ?? [];

include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Nueva Transacción</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/transactions.php?action=create" method="post" class="form">
    <div class="form-group">
        <label for="transaction_date">Fecha</label>
        <input type="date" name="transaction_date" id="transaction_date"
               value="<?= isset($oldData['transaction_date']) ? Sanitizer::cleanString($oldData['transaction_date']) : date('Y-m-d') ?>"
               required>
        <?php if (!empty($errors['transaction_date'])): ?>
            <small class="error"><?= htmlspecialchars($errors['transaction_date']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="description">Descripción</label>
        <input type="text" name="description" id="description"
               value="<?= isset($oldData['description']) ? Sanitizer::cleanString($oldData['description']) : '' ?>"
               required>
        <?php if (!empty($errors['description'])): ?>
            <small class="error"><?= htmlspecialchars($errors['description']) ?></small>
        <?php endif; ?>
    </div>

    <h3>Partida doble (1 débito y 1 crédito)</h3>

    <div class="form-group">
        <label for="debit_account_id">Cuenta de Débito</label>
        <select name="debit_account_id" id="debit_account_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($accounts as $acc): ?>
                <option value="<?= (int)$acc['id'] ?>"
                    <?= (isset($oldData['debit_account_id']) && (int)$oldData['debit_account_id'] === (int)$acc['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($acc['code'] . ' - ' . $acc['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['debit_account_id'])): ?>
            <small class="error"><?= htmlspecialchars($errors['debit_account_id']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="credit_account_id">Cuenta de Crédito</label>
        <select name="credit_account_id" id="credit_account_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($accounts as $acc): ?>
                <option value="<?= (int)$acc['id'] ?>"
                    <?= (isset($oldData['credit_account_id']) && (int)$oldData['credit_account_id'] === (int)$acc['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($acc['code'] . ' - ' . $acc['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['credit_account_id'])): ?>
            <small class="error"><?= htmlspecialchars($errors['credit_account_id']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="amount">Monto</label>
        <input type="number" step="0.01" min="0.01" name="amount" id="amount"
               value="<?= isset($oldData['amount']) ? Sanitizer::cleanString($oldData['amount']) : '' ?>"
               required>
        <?php if (!empty($errors['amount'])): ?>
            <small class="error"><?= htmlspecialchars($errors['amount']) ?></small>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="<?= BASE_URL ?>/transactions.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
