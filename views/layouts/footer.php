<?php
// views/layouts/footer.php
?>
</main>

<footer class="main-footer">
    <div class="footer-inner">
        <span>&copy; <?= date('Y') ?> Sistema Financiero. Todos los derechos reservados.</span>
        <span class="footer-secondary">
            Proyecto académico - <?= htmlspecialchars($appOwner ?? 'UTP') ?>
        </span>
    </div>
</footer>
</body>
</html>
