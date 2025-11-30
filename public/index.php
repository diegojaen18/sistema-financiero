<?php
/**
 * Punto de entrada público - Página principal
 * Sistema Financiero - UTP
 */

// Cargar configuración
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Inicio</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
    <div class="container" style="margin-top: 50px;">
        <div style="text-align: center;">
            <div style="font-size: 72px;">💼</div>
            <h1>Sistema Financiero de Contabilidad</h1>
            <h2>Universidad Tecnológica de Panamá</h2>
            <p class="text-muted">Facultad de Ingeniería en Sistemas Computacionales</p>
        </div>
        
        <div class="card mt-3" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header">
                ¿Por qué registrar tus transacciones?
            </div>
            <div class="card-body">
                
                <p><strong>La contabilidad organizada permite:</strong></p>
                
                <ul style="line-height: 2;">
                    <li>✓ Tomar decisiones informadas basadas en datos reales</li>
                    <li>✓ Cumplir con obligaciones fiscales y legales</li>
                    <li>✓ Detectar fraudes y errores oportunamente</li>
                    <li>✓ Planificar el futuro financiero de tu empresa</li>
                    <li>✓ Obtener financiamiento con reportes confiables</li>
                    <li>✓ Medir el rendimiento del negocio</li>
                </ul>
                
                <hr>
                
                <h3>Nuestro sistema te ofrece:</h3>
                
                <div class="row mt-2">
                    <div class="col-6">
                        <p>✓ Registro de transacciones con partida doble</p>
                        <p>✓ Generación automática de reportes financieros</p>
                        <p>✓ Control de acceso por roles</p>
                    </div>
                    <div class="col-6">
                        <p>✓ Firma digital de documentos</p>
                        <p>✓ Auditoría completa de operaciones</p>
                        <p>✓ Interfaz intuitiva y fácil de usar</p>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        Ingresar al Sistema →
                    </a>
                </div>
                
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                <strong><?= APP_NAME ?></strong> versión <?= APP_VERSION ?><br>
                Departamento de Ingeniería de Software<br>
                © <?= date('Y') ?> - Todos los derechos reservados
            </small>
        </div>
        
    </div>
    
</body>
</html>