<?php
// partials/topbar.php
// Requiere $page_title definida en la página que lo incluye.
$page_title = $page_title ?? 'Dashboard';
$initials = '';
if (!empty($_SESSION['user_name'])) {
    $parts = explode(' ', trim($_SESSION['user_name']));
    $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
}
?>
<header class="topbar">
    <h1 class="font-display text-lg font-semibold text-heading"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>

    <div class="flex items-center gap-2 relative">

        <!-- Notificaciones -->
        <div class="relative">
            <button type="button" id="notifBtn" class="icon-btn" aria-label="Notificaciones">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                <span class="notif-dot"></span>
            </button>

            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-72 card shadow-elevated z-20">
                <div class="p-3 border-b" style="border-color: var(--color-border);">
                    <p class="text-sm font-semibold text-heading">Notificaciones</p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-xs text-dim">Aún no hay notificaciones.</p>
                </div>
            </div>
        </div>

        <div class="w-px h-6 mx-1" style="background-color: var(--color-border);"></div>

        <!-- Usuario + logout -->
        <div class="flex items-center gap-3">
            <div class="avatar"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></div>
            <a href="../../logic/logout.php" class="icon-btn" aria-label="Cerrar sesión" title="Cerrar sesión">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
            </a>
        </div>
    </div>
</header>

<script>
    (function() {
        const btn = document.getElementById('notifBtn');
        const dropdown = document.getElementById('notifDropdown');

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== btn) {
                dropdown.classList.add('hidden');
            }
        });
    })();
</script>