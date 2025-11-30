<?php
// views/accounts/edit.php

use App\Security\Sanitizer;

$errors  = $errors  ?? [];
$oldData = $oldData ?? [];

$values = [
    'code'          => $account['code'],
    'name'          => $account['name'],
    'account_class' => $account['account_class'],
    'account_type'  => $account['account_type'],
    'is_active'     => $account['is_active'],
];

$values = array_merge($values, $oldData);

$classLabels = [
    1 => '1 - Activo',
    2 => '2 - Pasivo',
    3 => '3 - Patrimonio',
    4 => '4 - Ingresos',
    5 => '5 - Gastos',
    6 => '6 - Costos',
    7 => '7 - Otros Gastos',
];

include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Editar Cuenta</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/accounts.php?action=edit&id=<?= (int)$account['id'] ?>" method="post" class="form">
    <div class="form-group">
        <label for="code">Código</label>
        <input type="text" name="code" id="code"
               value="<?= Sanitizer::cleanString($values['code']) ?>" required>
        <?php if (!empty($errors['code'])): ?>
            <small class="error"><?= htmlspecialchars($errors['code']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="name">Nombre de la cuenta</label>
        <input type="text" name="name" id="name"
               value="<?= Sanitizer::cleanString($values['name']) ?>" required>
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
                    <?= ((int)$values['account_class'] === $value) ? 'selected' : '' ?>>
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
            <option value="debit"  <?= ($values['account_type'] === 'debit') ? 'selected' : '' ?>>Débito</option>
            <option value="credit" <?= ($values['account_type'] === 'credit') ? 'selected' : '' ?>>Crédito</option>
        </select>
        <?php if (!empty($errors['account_type'])): ?>
            <small class="error"><?= htmlspecialchars($errors['account_type']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="is_active" value="1"
                <?= $values['is_active'] ? 'checked' : '' ?>>
            Cuenta activa
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="<?= BASE_URL ?>/accounts.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
