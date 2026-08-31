<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$active_page = 'dashboard';
$page_title = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php
    include __DIR__ . '/header.php';
    ?>
</head>

<body>

    <div class="app-shell">

        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content">

            <?php include __DIR__ . '/partials/topbar.php'; ?>

            <main class="p-6 space-y-6">

                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">
                        Hola, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0], ENT_QUOTES, 'UTF-8') ?> 👋
                    </h2>
                    <p class="text-muted text-sm mt-1">Este es el resumen de tu rendimiento académico.</p>
                </div>

                <!-- Widgets -->
                <div class="widget-grid">

                    <div class="widget-card">
                        <div class="widget-icon widget-icon-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3v18h18" />
                                <path d="M18.7 8 13 13.7l-4-4L3 16" />
                            </svg>
                        </div>
                        <p class="widget-label">Promedio general</p>
                        <p class="widget-value">—</p>
                        <p class="widget-sub">Aún no hay datos registrados</p>
                    </div>

                    <div class="widget-card">
                        <div class="widget-icon widget-icon-danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 17 13.5 8.5 8.5 13.5 2 7" />
                                <polyline points="16 17 22 17 22 11" />
                            </svg>
                        </div>
                        <p class="widget-label">Materia con bajo desempeño</p>
                        <p class="widget-value">—</p>
                        <p class="widget-sub">
                            <span class="badge-pill badge-pill-danger">Sin datos</span>
                        </p>
                    </div>

                    <div class="widget-card">
                        <div class="widget-icon widget-icon-success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                                <polyline points="16 7 22 7 22 13" />
                            </svg>
                        </div>
                        <p class="widget-label">Materia con buen desempeño</p>
                        <p class="widget-value">—</p>
                        <p class="widget-sub">
                            <span class="badge-pill badge-pill-success">Sin datos</span>
                        </p>
                    </div>

                    <div class="widget-card">
                        <div class="widget-icon widget-icon-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <p class="widget-label">Periodo activo</p>
                        <p class="widget-value">—</p>
                        <p class="widget-sub">Ningún periodo configurado</p>
                    </div>

                </div>

                <!-- Placeholder para futura tabla de materias -->
                <div class="card p-6 text-center">
                    <p class="text-muted text-sm">Aquí irá el detalle por materia una vez conectemos los datos.</p>
                </div>

            </main>
        </div>
    </div>

</body>

</html>