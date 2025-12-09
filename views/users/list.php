<?php
// views/users/list.php
include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Usuarios administrativos</h1>

<?php if (!empty($_GET['msg'])): ?>
    <?php
    $msg  = $_GET['msg'];
    $text = '';
    if ($msg === 'created')   $text = 'Usuario creado correctamente.';
    if ($msg === 'updated')   $text = 'Usuario actualizado correctamente.';
    if ($msg === 'status')    $text = 'Estado del usuario actualizado.';
    if ($msg === 'notfound')  $text = 'Usuario no encontrado.';
    ?>
    <?php if ($text): ?>
        <div class="alert"><?= htmlspecialchars($text) ?></div>
    <?php endif; ?>
<?php endif; ?>

<section class="page-header">
    <p class="page-header-subtitle">
        Administra los usuarios del sistema. Usa el buscador para filtrar por nombre, usuario o correo electrónico.
    </p>

    <form class="search-form" method="get" action="<?= BASE_URL ?>/users.php">
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

<a href="<?= BASE_URL ?>/users.php?action=create" class="btn btn-primary" style="margin-bottom:1rem;">Nuevo usuario</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Nombre completo</th>
            <th>Email</th>
            <th>Activo</th>
            <th>Creado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($users)): ?>
        <tr><td colspan="7">No hay usuarios registrados.</td></tr>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= (int)$user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['is_active'] ? 'Sí' : 'No' ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/users.php?action=edit&id=<?= (int)$user['id'] ?>">Editar</a> |
                    <a href="<?= BASE_URL ?>/users.php?action=toggle&id=<?= (int)$user['id'] ?>">
                        <?= $user['is_active'] ? 'Desactivar' : 'Activar' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
