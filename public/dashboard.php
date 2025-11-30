<?php
/**
 * Dashboard Principal
 * Sistema Financiero - UTP
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../src/Security/SessionManager.php';
require_once __DIR__ . '/../src/Database/Connection.php';

use SistemaFinanciero\Security\SessionManager;
use SistemaFinanciero\Database\Connection;

// Verificar autenticación
if (!SessionManager::isActive()) {
    header('Location: login.php');
    exit;
}

$db = Connection::getInstance();
$userId = SessionManager::getUserId();
$username = SessionManager::getUsername();
$fullName = SessionManager::getFullName();
$roles = SessionManager::getRoles();

// Obtener estadísticas
try {
    // Total de usuarios activos
    $totalUsers = $db->queryScalar("SELECT COUNT(*) FROM users WHERE is_active = 1");
    
    // Total de cuentas activas
    $totalAccounts = $db->queryScalar("SELECT COUNT(*) FROM accounts WHERE is_active = 1");
    
    // Total de transacciones
    $totalTransactions = $db->queryScalar("SELECT COUNT(*) FROM transactions");
    
    // Total de transacciones publicadas
    $totalPosted = $db->queryScalar("SELECT COUNT(*) FROM transactions WHERE is_posted = 1");
    
    // Últimas transacciones
    $recentTransactions = $db->query(
        "SELECT t.*, u.full_name as created_by_name,
                (SELECT SUM(debit) FROM transaction_lines WHERE transaction_id = t.id) as total_debit,
                (SELECT SUM(credit) FROM transaction_lines WHERE transaction_id = t.id) as total_credit
         FROM transactions t
         INNER JOIN users u ON t.created_by = u.id
         ORDER BY t.created_at DESC
         LIMIT 5"
    );
    
} catch (Exception $e) {
    $error = "Error al cargar estadísticas: " . $e->getMessage();
}

// Incluir header
include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . '/../views/layouts/nav.php';
?>

<div class="container mt-3">
    
    <h2>Bienvenido, <?= htmlspecialchars($fullName) ?></h2>
    <p class="text-muted">Panel de Control - Sistema Financiero</p>
    
    <!-- Estadísticas -->
    <div class="stats-grid mt-3">
        
        <div class="stat-card blue">
            <h3>Usuarios Activos</h3>
            <div class="value"><?= $totalUsers ?? 0 ?></div>
        </div>
        
        <div class="stat-card green">
            <h3>Cuentas Contables</h3>
            <div class="value"><?= $totalAccounts ?? 0 ?></div>
        </div>
        
        <div class="stat-card orange">
            <h3>Transacciones Totales</h3>
            <div class="value"><?= $totalTransactions ?? 0 ?></div>
        </div>
        
        <div class="stat-card red">
            <h3>Transacciones Publicadas</h3>
            <div class="value"><?= $totalPosted ?? 0 ?></div>
        </div>
        
    </div>
    
    <!-- Últimas Transacciones -->
    <div class="card mt-3">
        <div class="card-header">
            Últimas Transacciones
        </div>
        <div class="card-body">
            
            <?php if (!empty($recentTransactions)): ?>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Débito</th>
                                <th>Crédito</th>
                                <th>Estado</th>
                                <th>Registrado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $tx): ?>
                                <tr>
                                    <td><?= $tx['id'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($tx['transaction_date'])) ?></td>
                                    <td><?= htmlspecialchars($tx['description']) ?></td>
                                    <td class="text-right">$<?= number_format($tx['total_debit'], 2) ?></td>
                                    <td class="text-right">$<?= number_format($tx['total_credit'], 2) ?></td>
                                    <td>
                                        <?php if ($tx['is_posted']): ?>
                                            <span class="badge badge-success">Publicada</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Borrador</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($tx['created_by_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-2">
                    <a href="transactions.php" class="btn btn-primary">Ver Todas las Transacciones</a>
                </div>
                
            <?php else: ?>
                <p class="text-center text-muted">No hay transacciones registradas</p>
            <?php endif; ?>
            
        </div>
    </div>
    
    <!-- Acciones Rápidas -->
    <div class="card mt-3">
        <div class="card-header">
            Acciones Rápidas
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <a href="transactions.php?action=create" class="btn btn-success btn-block">
                        ➕ Nueva Transacción
                    </a>
                </div>
                <div class="col-3">
                    <a href="accounts.php" class="btn btn-primary btn-block">
                        📊 Catálogo de Cuentas
                    </a>
                </div>
                <div class="col-3">
                    <a href="reports.php" class="btn btn-warning btn-block">
                        📈 Generar Reportes
                    </a>
                </div>
                <div class="col-3">
                    <a href="users.php" class="btn btn-secondary btn-block">
                        👥 Gestionar Usuarios
                    </a>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include __DIR__ . '/../views/layouts/footer.php'; ?>