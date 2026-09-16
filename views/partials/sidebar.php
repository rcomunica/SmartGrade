<?php
// partials/sidebar.php
// Requiere que $active_page esté definida en la página que lo incluye
// para resaltar el ítem de navegación activo.
$active_page = $active_page ?? '';
$nav_base = $nav_base ?? '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand-badge">S</div>
        <span class="sidebar-brand-text">SmartGrade</span>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= htmlspecialchars($nav_base, ENT_QUOTES, 'UTF-8') ?>dashboard.php" class="nav-item <?= $active_page === 'dashboard' ? 'is-active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="9" />
                <rect x="14" y="3" width="7" height="5" />
                <rect x="14" y="12" width="7" height="9" />
                <rect x="3" y="16" width="7" height="5" />
            </svg>
            <span class="nav-label">Dashboard</span>
        </a>

        <a href="<?= htmlspecialchars($nav_base, ENT_QUOTES, 'UTF-8') ?>grades/index.php" class="nav-item <?= $active_page === 'notas' ? 'is-active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <path d="M14 2v6h6" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <line x1="10" y1="9" x2="8" y2="9" />
            </svg>
            <span class="nav-label">Mis notas</span>
        </a>

        <a href="<?= htmlspecialchars($nav_base, ENT_QUOTES, 'UTF-8') ?>terms/index.php" class="nav-item <?= $active_page === 'periodos' ? 'is-active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <span class="nav-label">Periodos</span>
        </a>

        <a href="<?= htmlspecialchars($nav_base, ENT_QUOTES, 'UTF-8') ?>goals/index.php" class="nav-item <?= $active_page === 'metas' ? 'is-active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <circle cx="12" cy="12" r="6" />
                <circle cx="12" cy="12" r="2" />
            </svg>
            <span class="nav-label">Metas</span>
        </a>

        <a href="<?= htmlspecialchars($nav_base, ENT_QUOTES, 'UTF-8') ?>subjets/index.php" class="nav-item <?= $active_page === 'materias' ? 'is-active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
            </svg>
            <span class="nav-label">Materias</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="Colapsar menú">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="11 17 6 12 11 7" />
                <polyline points="18 17 13 12 18 7" />
            </svg>
        </button>
        <span class="sidebar-footer-text text-dim" style="font-size: 0.75rem;">Colapsar menú</span>
    </div>
</aside>

<script>
    (function() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const STORAGE_KEY = 'sidebar_collapsed';

        // Restaurar preferencia guardada
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            sidebar.classList.add('is-collapsed');
        }

        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('is-collapsed');
            localStorage.setItem(STORAGE_KEY, sidebar.classList.contains('is-collapsed') ? '1' : '0');
        });
    })();
</script>