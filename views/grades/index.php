<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/grades/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$active_page = 'notas';
$page_title = 'Mis notas';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$success = $_SESSION['grades_success'] ?? null;
unset($_SESSION['grades_success']);

$error = $_SESSION['grades_error'] ?? null;
unset($_SESSION['grades_error']);

$old = $_SESSION['grades_old'] ?? [
    'subjet_id' => '',
    'term_id' => '',
    'name' => '',
    'value' => '',
    'percentage' => '',
];
unset($_SESSION['grades_old']);

$subjets = getUserSubjetsOptions($pdo, $user_id);
$terms = getUserTermsOptions($pdo, $user_id);
$grades = getUserGrades($pdo, $user_id);
$can_create_grade = !empty($subjets) && !empty($terms);
$grades_by_subjet = [];

foreach ($grades as $grade) {
    $subjet_key = (string) $grade['subjet_id'];

    if (!isset($grades_by_subjet[$subjet_key])) {
        $grades_by_subjet[$subjet_key] = [
            'subjet_name' => $grade['subjet_name'],
            'terms' => [],
            'weighted_sum' => 0.0,
            'percentage_sum' => 0.0,
        ];
    }

    $term_key = (string) $grade['term_id'];
    if (!isset($grades_by_subjet[$subjet_key]['terms'][$term_key])) {
        $grades_by_subjet[$subjet_key]['terms'][$term_key] = [
            'term_name' => $grade['term_name'],
            'grades' => [],
            'weighted_sum' => 0.0,
            'percentage_sum' => 0.0,
        ];
    }

    $grade_value = (float) $grade['value'];
    $grade_percentage = (float) $grade['percentage'];

    $grades_by_subjet[$subjet_key]['terms'][$term_key]['grades'][] = $grade;
    $grades_by_subjet[$subjet_key]['terms'][$term_key]['weighted_sum'] += $grade_value * $grade_percentage;
    $grades_by_subjet[$subjet_key]['terms'][$term_key]['percentage_sum'] += $grade_percentage;
    $grades_by_subjet[$subjet_key]['weighted_sum'] += $grade_value * $grade_percentage;
    $grades_by_subjet[$subjet_key]['percentage_sum'] += $grade_percentage;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Mis notas | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Gestión de notas</h2>
                    <p class="text-muted text-sm mt-1">Registra, edita y elimina las notas de tus materias por periodo.</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if (!$can_create_grade): ?>
                    <div class="alert alert-error">
                        Para registrar notas primero debes crear al menos una materia y un periodo.
                    </div>
                <?php endif; ?>

                <section class="card p-6">
                    <h3 class="font-display text-lg font-semibold text-heading mb-5">Agregar nota</h3>

                    <form action="../../logic/grades/create.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                        <div>
                            <label for="subjet_id" class="field-label">Materia</label>
                            <select id="subjet_id" name="subjet_id" class="input" <?= $can_create_grade ? '' : 'disabled' ?> required>
                                <option value="">Selecciona una materia</option>
                                <?php foreach ($subjets as $subjet): ?>
                                    <option value="<?= (int) $subjet['id'] ?>" <?= $old['subjet_id'] === (string) $subjet['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subjet['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="term_id" class="field-label">Periodo</label>
                            <select id="term_id" name="term_id" class="input" <?= $can_create_grade ? '' : 'disabled' ?> required>
                                <option value="">Selecciona un periodo</option>
                                <?php foreach ($terms as $term): ?>
                                    <option value="<?= (int) $term['id'] ?>" <?= $old['term_id'] === (string) $term['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($term['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="name" class="field-label">Nombre de la nota</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                maxlength="100"
                                required
                                placeholder="Ej: Examen 1"
                                value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>"
                                class="input"
                                <?= $can_create_grade ? '' : 'disabled' ?>>
                        </div>

                        <div>
                            <label for="value" class="field-label">Valor (1.0 a 5.0)</label>
                            <input
                                type="number"
                                id="value"
                                name="value"
                                min="1"
                                max="5"
                                step="0.1"
                                required
                                placeholder="Ej: 2.9"
                                value="<?= htmlspecialchars($old['value'], ENT_QUOTES, 'UTF-8') ?>"
                                class="input"
                                <?= $can_create_grade ? '' : 'disabled' ?>>
                        </div>

                        <div>
                            <label for="percentage" class="field-label">Porcentaje (%)</label>
                            <input
                                type="number"
                                id="percentage"
                                name="percentage"
                                min="0.01"
                                max="100"
                                step="0.01"
                                required
                                placeholder="Ej: 20"
                                value="<?= htmlspecialchars($old['percentage'], ENT_QUOTES, 'UTF-8') ?>"
                                class="input"
                                <?= $can_create_grade ? '' : 'disabled' ?>>
                        </div>

                        <div class="md:col-span-2">
                            <button type="submit" class="btn btn-primary" style="width: auto;" <?= $can_create_grade ? '' : 'disabled' ?>>Agregar nota</button>
                        </div>
                    </form>
                </section>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                        <h3 class="font-display text-lg font-semibold text-heading">Mis notas</h3>
                        <span class="text-xs text-dim"><?= count($grades) ?> registradas</span>
                    </div>

                    <?php if (empty($grades)): ?>
                        <p class="text-sm text-muted">Aún no tienes notas registradas.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($grades_by_subjet as $group): ?>
                                <?php
                                $percentage_sum = (float) $group['percentage_sum'];
                                $average = $percentage_sum > 0 ? ((float) $group['weighted_sum'] / $percentage_sum) : 0;
                                $is_passing = $average >= 3.0;
                                ?>
                                <details class="card" style="border-radius: var(--radius-lg);">
                                    <summary class="p-4 cursor-pointer flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-heading font-medium"><?= htmlspecialchars($group['subjet_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-xs text-dim mt-1"><?= count($group['terms']) ?> periodos con calificaciones</p>
                                        </div>
                                        <span class="badge-pill <?= $is_passing ? 'badge-pill-success' : 'badge-pill-danger' ?>">
                                            Promedio materia: <?= htmlspecialchars(number_format($average, 2), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </summary>

                                    <div class="px-4 pb-4">
                                        <div class="space-y-2">
                                            <?php foreach ($group['terms'] as $term_group): ?>
                                                <?php
                                                $term_percentage_sum = (float) $term_group['percentage_sum'];
                                                $term_average = $term_percentage_sum > 0 ? ((float) $term_group['weighted_sum'] / $term_percentage_sum) : 0;
                                                $term_is_passing = $term_average >= 3.0;
                                                ?>
                                                <details class="rounded-lg border" style="border-color: var(--color-border);">
                                                    <summary class="p-3 cursor-pointer flex items-center justify-between gap-3" style="background-color: var(--color-overlay-soft);">
                                                        <p class="text-sm text-heading font-medium"><?= htmlspecialchars($term_group['term_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                                        <span class="badge-pill <?= $term_is_passing ? 'badge-pill-success' : 'badge-pill-danger' ?>">
                                                            Promedio periodo: <?= htmlspecialchars(number_format($term_average, 2), ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    </summary>

                                                    <div class="p-3 space-y-2">
                                                        <?php foreach ($term_group['grades'] as $grade): ?>
                                                            <div class="p-3 rounded-lg flex items-center justify-between gap-3 flex-wrap" style="background-color: var(--color-overlay-soft);">
                                                                <div class="flex-1 min-w-[220px]">
                                                                    <p class="text-sm text-heading font-medium"><?= htmlspecialchars($grade['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                </div>

                                                                <div class="text-sm text-muted">
                                                                    Nota: <span class="text-heading font-medium"><?= htmlspecialchars(number_format((float) $grade['value'], 2), ENT_QUOTES, 'UTF-8') ?></span>
                                                                </div>

                                                                <div class="text-sm text-muted">
                                                                    Porcentaje: <span class="text-heading font-medium"><?= htmlspecialchars(number_format((float) $grade['percentage'], 2), ENT_QUOTES, 'UTF-8') ?>%</span>
                                                                </div>

                                                                <div class="flex items-center gap-2">
                                                                    <a href="edit.php?id=<?= (int) $grade['id'] ?>" class="text-sm link-accent">Editar</a>
                                                                    <form action="../../logic/grades/delete.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta nota?');">
                                                                        <input type="hidden" name="id" value="<?= (int) $grade['id'] ?>">
                                                                        <button type="submit" class="btn btn-danger-ghost" style="padding: 0.35rem 0.7rem;">Eliminar</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>

                                                        <div class="pt-2 border-t flex items-center justify-between" style="border-color: var(--color-border);">
                                                            <p class="text-sm text-muted">Promedio final del periodo</p>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-heading font-semibold"><?= htmlspecialchars(number_format($term_average, 2), ENT_QUOTES, 'UTF-8') ?></span>
                                                                <span class="badge-pill <?= $term_is_passing ? 'badge-pill-success' : 'badge-pill-danger' ?>">
                                                                    <?= $term_is_passing ? 'Aprobado' : 'Reprobado' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </details>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="mt-3 pt-3 border-t flex items-center justify-between" style="border-color: var(--color-border);">
                                            <p class="text-sm text-muted">Promedio final de la materia</p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-heading font-semibold"><?= htmlspecialchars(number_format($average, 2), ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="badge-pill <?= $is_passing ? 'badge-pill-success' : 'badge-pill-danger' ?>">
                                                    <?= $is_passing ? 'Aprobada' : 'Reprobada' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
</body>

</html>