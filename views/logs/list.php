<?php
// views/logs/list.php
include BASE_PATH . '/views/layouts/header.php';
?>

<h1>Logs del sistema</h1>

<p class="page-header-subtitle">
    Registro de transacciones con fines de auditoría. Módulo de solo lectura.
</p>

<form class="search-form" method="get" action="<?= BASE_URL ?>/logs.php">
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
            placeholder="Buscar en logs (fecha, descripción, usuario, etc.)..."
            value="<?= htmlspecialchars($currentSearch ?? '') ?>"
        >
        <button type="submit" class="btn btn-primary btn-small btn-search">
            Buscar
        </button>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Débitos</th>
            <th>Créditos</th>
            <th>Usuario</th>
            <th>Posteada</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($logs)): ?>
        <tr><td colspan="7">No hay registros que coincidan con la búsqueda.</td></tr>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= (int)$log['id'] ?></td>
                <td><?= htmlspecialchars($log['transaction_date']) ?></td>
                <td><?= htmlspecialchars($log['description']) ?></td>
                <td><?= number_format((float)($log['total_debit'] ?? 0), 2) ?></td>
                <td><?= number_format((float)($log['total_credit'] ?? 0), 2) ?></td>
                <td><?= htmlspecialchars($log['created_by_username'] ?? '-') ?></td>
                <td>
                    <?php if (!empty($log['is_posted'])): ?>
                        <span class="badge badge-success">Sí</span>
                    <?php else: ?>
                        <span class="badge badge-warning">No</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>
