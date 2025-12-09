<?php
// views/roles/manage.php
include BASE_PATH . '/views/layouts/header.php';

// Tomamos el primer rol (si tiene) como el rol actual
$currentRoleId = !empty($userRoleIds) ? (int)$userRoleIds[0] : 0;
?>

<h1>Gestionar roles</h1>

<div class="card">
    <p><strong>Usuario:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($user['email']) ?></p>
</div>

<form action="<?= BASE_URL ?>/roles.php?action=edit&id=<?= (int)$user['id'] ?>" method="post" class="form">
    <h3>Rol asignado</h3>
    <p class="small-note">
        Cada usuario solo puede tener <strong>un rol</strong> en el sistema.
    </p>

    <?php if (empty($roles)): ?>
        <p>No hay roles definidos en el sistema.</p>
    <?php else: ?>
        <?php foreach ($roles as $role): ?>
            <div class="form-group">
                <label>
                    <input
                        type="radio"
                        name="role_id"
                        value="<?= (int)$role['id'] ?>"
                        <?= ($currentRoleId === (int)$role['id']) ? 'checked' : '' ?>
                    >
                    <?= htmlspecialchars($role['name']) ?>
                </label>
                <?php if (!empty($role['description'])): ?>
                    <span class="small-note"><?= htmlspecialchars($role['description']) ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="form-group">
            <label>
                <input
                    type="radio"
                    name="role_id"
                    value="0"
                    <?= $currentRoleId === 0 ? 'checked' : '' ?>
                >
                Sin rol asignado
            </label>
        </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Guardar rol</button>
    <a href="<?= BASE_URL ?>/roles.php" class="btn btn-secondary">Volver</a>
</form>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
