<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/grades/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    $_SESSION['grades_error'] = 'Nota inválida.';
    header('Location: index.php');
    exit;
}

$active_page = 'notas';
$page_title = 'Editar nota';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$grade = getUserGradeById($pdo, $user_id, (int) $id);
if (!$grade) {
    $_SESSION['grades_error'] = 'No se encontró la nota seleccionada.';
    header('Location: index.php');
    exit;
}

$subjets = getUserSubjetsOptions($pdo, $user_id);
$terms = getUserTermsOptions($pdo, $user_id);
$can_edit_grade = !empty($subjets) && !empty($terms);

$error = $_SESSION['grades_error'] ?? null;
unset($_SESSION['grades_error']);

$old = $_SESSION['grades_old'] ?? null;
unset($_SESSION['grades_old']);

$subjet_value = $old['subjet_id'] ?? (string) $grade['subjet_id'];
$term_value = $old['term_id'] ?? (string) $grade['term_id'];
$name_value = $old['name'] ?? $grade['name'];
$value_value = $old['value'] ?? (string) $grade['value'];
$percentage_value = $old['percentage'] ?? (string) $grade['percentage'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar nota | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Editar nota</h2>
                    <p class="text-muted text-sm mt-1">Actualiza la información de la nota seleccionada.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if (!$can_edit_grade): ?>
                    <div class="alert alert-error">
                        Para editar notas debes tener al menos una materia y un periodo.
                    </div>
                <?php endif; ?>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
                        <h3 class="font-display text-lg font-semibold text-heading">Actualizar nota</h3>
                        <a href="index.php" class="text-sm link-accent">Volver al listado</a>
                    </div>

                    <form action="../../logic/grades/update.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                        <input type="hidden" name="id" value="<?= (int) $grade['id'] ?>">

                        <div>
                            <label for="subjet_id" class="field-label">Materia</label>
                            <select id="subjet_id" name="subjet_id" class="input" <?= $can_edit_grade ? '' : 'disabled' ?> required>
                                <option value="">Selecciona una materia</option>
                                <?php foreach ($subjets as $subjet): ?>
                                    <option value="<?= (int) $subjet['id'] ?>" <?= $subjet_value === (string) $subjet['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subjet['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="term_id" class="field-label">Periodo</label>
                            <select id="term_id" name="term_id" class="input" <?= $can_edit_grade ? '' : 'disabled' ?> required>
                                <option value="">Selecciona un periodo</option>
                                <?php foreach ($terms as $term): ?>
                                    <option value="<?= (int) $term['id'] ?>" <?= $term_value === (string) $term['id'] ? 'selected' : '' ?>>
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
                                value="<?= htmlspecialchars($name_value, ENT_QUOTES, 'UTF-8') ?>"
                                class="input"
                                <?= $can_edit_grade ? '' : 'disabled' ?>>
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
                                value="<?= htmlspecialchars($value_value, ENT_QUOTES, 'UTF-8') ?>"
                                class="input"
                                <?= $can_edit_grade ? '' : 'disabled' ?>>
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
                                value="<?= htmlspecialchars($percentage_value, ENT_QUOTES, 'UTF-8') ?>"
                                class="input"
                                <?= $can_edit_grade ? '' : 'disabled' ?>>
                        </div>

                        <div class="md:col-span-2">
                            <button type="submit" class="btn btn-primary" style="width: auto;" <?= $can_edit_grade ? '' : 'disabled' ?>>Guardar cambios</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
