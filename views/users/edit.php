<?php
// views/users/edit.php

use App\Security\Sanitizer;

$errors  = $errors  ?? [];
$oldData = $oldData ?? [];

$values = [
    'username'  => $user['username'],
    'full_name' => $user['full_name'],
    'email'     => $user['email'],
    'is_active' => $user['is_active'],
];

$values = array_merge($values, $oldData);

include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Editar usuario</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/users.php?action=edit&id=<?= (int)$user['id'] ?>" method="post" class="form">
    <div class="form-group">
        <label for="username">Usuario</label>
        <input type="text" name="username" id="username"
               value="<?= Sanitizer::cleanString($values['username']) ?>" required>
        <?php if (!empty($errors['username'])): ?>
            <small class="error"><?= htmlspecialchars($errors['username']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="full_name">Nombre completo</label>
        <input type="text" name="full_name" id="full_name"
               value="<?= Sanitizer::cleanString($values['full_name']) ?>" required>
        <?php if (!empty($errors['full_name'])): ?>
            <small class="error"><?= htmlspecialchars($errors['full_name']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" name="email" id="email"
               value="<?= Sanitizer::cleanString($values['email']) ?>" required>
        <?php if (!empty($errors['email'])): ?>
            <small class="error"><?= htmlspecialchars($errors['email']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="password">Nueva contraseña (opcional)</label>
        <input type="password" name="password" id="password">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmar contraseña (opcional)</label>
        <input type="password" name="password_confirmation" id="password_confirmation">
        <?php if (!empty($errors['password_confirmation'])): ?>
            <small class="error"><?= htmlspecialchars($errors['password_confirmation']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="is_active" value="1"
                <?= $values['is_active'] ? 'checked' : '' ?>>
            Usuario activo
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="<?= BASE_URL ?>/users.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
