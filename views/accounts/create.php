<?php
// views/accounts/create.php

use App\Security\Sanitizer;

$errors  = $errors  ?? [];
$oldData = $oldData ?? [];

include BASE_PATH . '/views/layouts/header.php';

$classLabels = [
    1 => '1 - Activo',
    2 => '2 - Pasivo',
    3 => '3 - Patrimonio',
    4 => '4 - Ingresos',
    5 => '5 - Gastos',
    6 => '6 - Costos',
    7 => '7 - Otros Gastos',
];
?>

<h1>Nueva Cuenta</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/accounts.php?action=create" method="post" class="form">
    <div class="form-group">
        <label for="code">Código</label>
        <input type="text" name="code" id="code"
               value="<?= isset($oldData['code']) ? Sanitizer::cleanString($oldData['code']) : '' ?>"
               required>
        <?php if (!empty($errors['code'])): ?>
            <small class="error"><?= htmlspecialchars($errors['code']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="name">Nombre de la cuenta</label>
        <input type="text" name="name" id="name"
               value="<?= isset($oldData['name']) ? Sanitizer::cleanString($oldData['name']) : '' ?>"
               required>
        <?php if (!empty($errors['name'])): ?>
            <small class="error"><?= htmlspecialchars($errors['name']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="account_class">Clase de cuenta</label>
        <select name="account_class" id="account_class" required>
            <option value="">Seleccione...</option>
            <?php foreach ($classLabels as $value => $label): ?>
                <option value="<?= $value ?>"
                    <?= (isset($oldData['account_class']) && (int)$oldData['account_class'] === $value) ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['account_class'])): ?>
            <small class="error"><?= htmlspecialchars($errors['account_class']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="account_type">Tipo de cuenta</label>
        <select name="account_type" id="account_type" required>
            <option value="">Seleccione...</option>
            <option value="debit"  <?= (isset($oldData['account_type']) && $oldData['account_type'] === 'debit') ? 'selected' : '' ?>>Débito</option>
            <option value="credit" <?= (isset($oldData['account_type']) && $oldData['account_type'] === 'credit') ? 'selected' : '' ?>>Crédito</option>
        </select>
        <?php if (!empty($errors['account_type'])): ?>
            <small class="error"><?= htmlspecialchars($errors['account_type']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="is_active" value="1"
                <?= (!isset($oldData['is_active']) || $oldData['is_active']) ? 'checked' : '' ?>>
            Cuenta activa
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="<?= BASE_URL ?>/accounts.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
