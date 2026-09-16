<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/goals/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    $_SESSION['goals_error'] = 'Meta inválida.';
    header('Location: index.php');
    exit;
}

$active_page = 'metas';
$page_title = 'Editar meta';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$goal = getUserGoalById($pdo, $user_id, (int) $id);
if (!$goal) {
    $_SESSION['goals_error'] = 'No se encontró la meta seleccionada.';
    header('Location: index.php');
    exit;
}

$error = $_SESSION['goals_error'] ?? null;
unset($_SESSION['goals_error']);

$old = $_SESSION['goals_old'] ?? null;
unset($_SESSION['goals_old']);

$subjects = getUserGoalSubjects($pdo, $user_id);
$status_labels = [
    'pending' => 'Pendiente',
    'in_progress' => 'En progreso',
    'completed' => 'Completada',
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar meta | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Editar meta</h2>
                    <p class="text-muted text-sm mt-1">Actualiza los datos y el progreso de tu meta.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
                        <h3 class="font-display text-lg font-semibold text-heading">Actualizar meta</h3>
                        <a href="index.php" class="text-sm link-accent">Volver al listado</a>
                    </div>

                    <form action="../../logic/goals/update.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                        <input type="hidden" name="id" value="<?= (int) $goal['id'] ?>">

                        <div>
                            <label for="subject_id" class="field-label">Materia</label>
                            <select id="subject_id" name="subject_id" required class="input">
                                <?php $subject_value = $old['subject_id'] ?? $goal['subject_id']; ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= (int) $subject['id'] ?>" <?= (string) $subject_value === (string) $subject['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="name" class="field-label">Nombre de la meta</label>
                            <input type="text" id="name" name="name" maxlength="100" required value="<?= htmlspecialchars($old['name'] ?? $goal['name'], ENT_QUOTES, 'UTF-8') ?>" class="input">
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="field-label">Descripción</label>
                            <textarea id="description" name="description" rows="3" class="input"><?= htmlspecialchars($old['description'] ?? ($goal['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div>
                            <label for="target_date" class="field-label">Fecha límite</label>
                            <input type="date" id="target_date" name="target_date" value="<?= htmlspecialchars($old['target_date'] ?? ($goal['target_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="input">
                        </div>

                        <div>
                            <label for="status" class="field-label">Estado</label>
                            <?php $status_value = $old['status'] ?? $goal['status']; ?>
                            <select id="status" name="status" class="input">
                                <?php foreach ($status_labels as $status => $label): ?>
                                    <option value="<?= $status ?>" <?= $status_value === $status ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <button type="submit" class="btn btn-primary" style="width: auto;">Guardar cambios</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
