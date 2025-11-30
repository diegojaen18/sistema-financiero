<?php
// views/users/create.php

use App\Security\Sanitizer;

$errors  = $errors  ?? [];
$oldData = $oldData ?? [];

include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Nuevo usuario</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/users.php?action=create" method="post" class="form">
    <div class="form-group">
        <label for="username">Usuario</label>
        <input type="text" name="username" id="username"
               value="<?= isset($oldData['username']) ? Sanitizer::cleanString($oldData['username']) : '' ?>"
               required>
        <?php if (!empty($errors['username'])): ?>
            <small class="error"><?= htmlspecialchars($errors['username']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="full_name">Nombre completo</label>
        <input type="text" name="full_name" id="full_name"
               value="<?= isset($oldData['full_name']) ? Sanitizer::cleanString($oldData['full_name']) : '' ?>"
               required>
        <?php if (!empty($errors['full_name'])): ?>
            <small class="error"><?= htmlspecialchars($errors['full_name']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" name="email" id="email"
               value="<?= isset($oldData['email']) ? Sanitizer::cleanString($oldData['email']) : '' ?>"
               required>
        <?php if (!empty($errors['email'])): ?>
            <small class="error"><?= htmlspecialchars($errors['email']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>
        <?php if (!empty($errors['password'])): ?>
            <small class="error"><?= htmlspecialchars($errors['password']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
        <?php if (!empty($errors['password_confirmation'])): ?>
            <small class="error"><?= htmlspecialchars($errors['password_confirmation']) ?></small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="is_active" value="1"
                <?= (!isset($oldData['is_active']) || $oldData['is_active']) ? 'checked' : '' ?>>
            Usuario activo
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="<?= BASE_URL ?>/users.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
