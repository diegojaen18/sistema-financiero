<?php
session_start();
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Financial System Login</h2>
        <?php if($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="AuthController.php" method="POST">
            <input type="text" name="username" placeholder="Enter username">
            <input type="password" name="password" placeholder="Enter password">
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
