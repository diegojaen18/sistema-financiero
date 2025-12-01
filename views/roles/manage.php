<?php
// views/roles/manage.php
include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Gestionar roles</h1>

<div class="card">
    <p><strong>Usuario:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($user['email']) ?></p>
</div>

<form action="<?= BASE_URL ?>/roles.php?action=edit&id=<?= (int)$user['id'] ?>" method="post" class="form">
    <h3>Roles disponibles</h3>
    <?php if (empty($roles)): ?>
        <p>No hay roles definidos en el sistema.</p>
    <?php else: ?>
        <?php foreach ($roles as $role): ?>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="roles[]" value="<?= (int)$role['id'] ?>"
                        <?= in_array($role['id'], $userRoleIds, true) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($role['name']) ?>
                </label>
                <?php if (!empty($role['description'])): ?>
                    <span class="small-note"><?= htmlspecialchars($role['description']) ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Guardar roles</button>
    <a href="<?= BASE_URL ?>/roles.php" class="btn btn-secondary">Volver</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
