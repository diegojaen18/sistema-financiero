<?php
// views/auth/login.php
// Variables esperadas: $errors, $pageTitle

use App\Security\Sanitizer;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Login'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

<div class="login-container">
    <h1 class="app-title"><?= APP_NAME ?></h1>
    <h2 class="login-title">Iniciar sesión</h2>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($errors['general']); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="login.php" class="login-form">
        <div class="form-group">
            <label for="username">Usuario</label>
            <input 
                type="text" 
                name="username" 
                id="username" 
                required
                value="<?= isset($_POST['username']) ? Sanitizer::cleanString($_POST['username']) : '' ?>"
            >
            <?php if (!empty($errors['username'])): ?>
                <small class="error"><?= htmlspecialchars($errors['username']); ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                required
            >
            <?php if (!empty($errors['password'])): ?>
                <small class="error"><?= htmlspecialchars($errors['password']); ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    <p class="login-footer">
        Usuario de prueba: <strong>admin</strong> / Contraseña: <strong>Admin123!</strong>
    </p>
</div>

</body>
</html>
