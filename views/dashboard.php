<?php
session_start();
require_once __DIR__ . '/../logic/db.php';
require_once __DIR__ . '/../logic/dashboard.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$active_page = 'inicio';
$page_title = 'Inicio';


$lowest_subject = get_lowest_subject($pdo);
$high_subject = get_high_subject($pdo);
$actual_term = get_actual_term($pdo);
$average_grade = (float) (get_avg_grade($pdo) ?? 0);
$average_progress = min(100, max(0, ($average_grade / 5) * 100));
$lowest_progress = min(100, max(0, (((float) ($lowest_subject['avg_grade'] ?? 0)) / 5) * 100));
$high_progress = min(100, max(0, (((float) ($high_subject['avg_grade'] ?? 0)) / 5) * 100));

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Inicio | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php
    include __DIR__ . '/header.php';
    ?>
</head>

<body>

    <div class="app-shell">

        <div class="main-content">

            <?php include __DIR__ . '/partials/topbar.php'; ?>

            <main class="p-6 space-y-6 dashboard-main">

                <div class="dashboard-intro">
                    <span class="eyebrow">Resumen académico</span>
                    <h2 class="font-display text-xl font-semibold text-heading">
                        Hola, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0], ENT_QUOTES, 'UTF-8') ?> 👋
                    </h2>
                    <p class="text-muted text-sm mt-1">Este es el resumen de tu rendimiento académico.</p>
                </div>

                <!-- Widgets -->
                <div class="widget-grid">

                    <div class="widget-card widget-card-score">
                        <div class="score-card-top">
                            <div class="widget-icon widget-icon-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3v18h18" />
                                    <path d="M18.7 8 13 13.7l-4-4L3 16" />
                                </svg>
                            </div>
                            <span class="score-state">Escala 1 a 5</span>
                        </div>
                        <div class="score-card-body">
                            <div class="score-ring" style="--score-progress: <?= htmlspecialchars((string) $average_progress, ENT_QUOTES, 'UTF-8') ?>%;">
                                <span><?= htmlspecialchars(number_format($average_grade, 2), ENT_QUOTES, 'UTF-8') ?></span>
                                <small>/ 5</small>
                            </div>
                            <div>
                                <p class="widget-label">Promedio general</p>
                                <p class="score-card-copy">Tu rendimiento<br>en el periodo activo.</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-card widget-card-focus">
                        <div class="widget-icon widget-icon-danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 17 13.5 8.5 8.5 13.5 2 7" />
                                <polyline points="16 17 22 17 22 11" />
                            </svg>
                        </div>
                        <p class="widget-label">Materia con bajo desempeño</p>
                        <p class="widget-value"><?= htmlspecialchars($lowest_subject["name"] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="signal-row">
                            <span class="signal-track"><span style="width: <?= htmlspecialchars((string) $lowest_progress, ENT_QUOTES, 'UTF-8') ?>%;"></span></span>
                            <span class="signal-value"><?= htmlspecialchars(number_format($lowest_subject["avg_grade"] ?? 0, 2), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <p class="widget-sub">Área que deberias tener mayor dedicación</p>
                    </div>

                    <div class="widget-card widget-card-strength">
                        <div class="widget-icon widget-icon-success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                                <polyline points="16 7 22 7 22 13" />
                            </svg>
                        </div>
                        <p class="widget-label">Materia con buen desempeño</p>
                        <p class="widget-value"><?= htmlspecialchars($high_subject["name"] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="signal-row">
                            <span class="signal-track"><span style="width: <?= htmlspecialchars((string) $high_progress, ENT_QUOTES, 'UTF-8') ?>%;"></span></span>
                            <span class="signal-value"><?= htmlspecialchars(number_format($high_subject["avg_grade"] ?? 0, 2), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <p class="widget-sub">Tu punto más fuerte</p>
                    </div>

                    <div class="widget-card widget-card-term" style="grid-column: span 6;">
                        <div class="widget-icon widget-icon-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <div class="term-card-content">
                            <div>
                                <p class="widget-label">Periodo activo</p>
                                <p class="term-index"><?= htmlspecialchars($actual_term["name"] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                        <span class="term-card-line"></span>
                        <p class="widget-sub">Vigente ahora</p>
                    </div>

                </div>
                <div class="dashboard-actions flex justify-end pt-2">
                    <form action="../logic/connectAi.php" class="w-full md:w-1/4" onsubmit="this.querySelector('button').disabled = true; this.querySelector('.ai-button-label').textContent = 'Generando reporte...'; this.querySelector('.ai-loading-spinner').hidden = false;">
                        <button type="submit" class="btn btn-primary">
                            <span class="ai-loading-spinner" aria-hidden="true" hidden></span>
                            <span class="ai-button-label">Generar reporte con AI</span>
                        </button>
                    </form>
                </div>
                <!-- Placeholder para futura tabla de materias -->
                <div class="card p-6 text-center dashboard-report">
                    <?php if (isset($_SESSION['ai_result'])) { ?>
                        <div class="ai-result">
                            <h1 class="">Ey <?= htmlspecialchars($_SESSION['user_name']) ?> aca tienes tu resultado:</h1>
                            <?= $_SESSION['ai_result'] ?>
                        </div>
                    <?php } else { ?>
                        <p class="text-muted text-sm">Haz clic en el botón "Generar reporte con AI" para obtener un resumen de tu rendimiento académico.</p>
                    <?php } ?>

                </div>

            </main>
        </div>
    </div>

</body>

</html>