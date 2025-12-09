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
<!-- Overlay genérico para pantallas emergentes -->
    <div id="app-modal-overlay" class="app-modal-overlay" style="display:none;">
        <div class="app-modal-backdrop"></div>
        <div class="app-modal-dialog">
            <h2 id="app-modal-title">Título</h2>
            <p id="app-modal-message"></p>
            <pre id="app-modal-extra" class="app-modal-extra"></pre>
            <button type="button" class="btn btn-primary btn-small" id="app-modal-close">
                Entendido
            </button>
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('app-modal-overlay');
            const titleEl = document.getElementById('app-modal-title');
            const msgEl   = document.getElementById('app-modal-message');
            const extraEl = document.getElementById('app-modal-extra');
            const btn     = document.getElementById('app-modal-close');

            function showModal(title, message, extraText) {
                if (!overlay) return;
                titleEl.textContent = title || '';
                msgEl.textContent   = message || '';
                if (extraText) {
                    extraEl.textContent = extraText;
                    extraEl.style.display = 'block';
                } else {
                    extraEl.textContent = '';
                    extraEl.style.display = 'none';
                }
                overlay.style.display = 'flex';
                document.body.classList.add('has-modal');
            }

            function hideModal() {
                if (!overlay) return;
                overlay.style.display = 'none';
                document.body.classList.remove('has-modal');
                // Limpia el hash de la URL si existe
                if (window.history && window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('hash');
                    window.history.replaceState({}, document.title, url.toString());
                }
            }

            if (btn) {
                btn.addEventListener('click', hideModal);
            }

            window.AppModal = { show: showModal, hide: hideModal };
        })();
    </script>
</body>
</html>
