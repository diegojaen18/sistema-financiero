<?php
// views/roles/users.php
include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Roles de Usuarios</h1>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'updated')   $text = 'Roles actualizados correctamente.';
    if ($msg === 'notfound')  $text = 'Usuario no encontrado.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<p class="small-note">
    Desde esta pantalla, solo el <strong>Administrador</strong> puede asignar o revocar roles
    como Contador, Gerente Financiero o Auditor.
</p>

<section class="page-header">

    <form class="search-form" method="get" action="<?= BASE_URL ?>/roles.php">
        <input type="hidden" name="action" value="list">
        <div class="search-group">
            <span class="search-icon" aria-hidden="true">
                <svg viewBox="0 0 20 20">
                    <circle cx="8.5" cy="8.5" r="5.5"></circle>
                    <line x1="12" y1="12" x2="17" y2="17"></line>
                </svg>
            </span>
            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Buscar usuario..."
                value="<?= htmlspecialchars($currentSearch ?? '') ?>"
            >
            <button type="submit" class="btn btn-primary btn-small btn-search">
                Buscar
            </button>
        </div>
    </form>
</section>

<table class="table">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Activo</th>
            <th>Rol asignado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($users)): ?>
        <tr><td colspan="6">No hay usuarios registrados.</td></tr>
    <?php else: ?>
        <?php foreach ($users as $u): ?>
            <?php
                $roles = $rolesByUser[$u['id']] ?? [];
                $roleNames = $roles ? implode(', ', array_column($roles, 'name')) : '—';
            ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['is_active'] ? 'Sí' : 'No' ?></td>
                <td><?= htmlspecialchars($roleNames) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/roles.php?action=edit&id=<?= (int)$u['id'] ?>">Gestionar rol</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
