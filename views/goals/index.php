<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/goals/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$active_page = 'metas';
$page_title = 'Mis metas';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$success = $_SESSION['goals_success'] ?? null;
unset($_SESSION['goals_success']);

$error = $_SESSION['goals_error'] ?? null;
unset($_SESSION['goals_error']);

$old = $_SESSION['goals_old'] ?? [
    'subject_id' => '',
    'name' => '',
    'description' => '',
    'target_date' => '',
    'status' => 'pending',
];
unset($_SESSION['goals_old']);

$goals = getUserGoals($pdo, $user_id);
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
    <title>Mis metas | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Gestión de metas</h2>
                    <p class="text-muted text-sm mt-1">Define, actualiza y da seguimiento a tus objetivos académicos.</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?php if (empty($subjects)): ?>
                    <div class="alert alert-error">Registra al menos una materia antes de agregar una meta.</div>
                <?php else: ?>
                    <section class="card p-6">
                        <h3 class="font-display text-lg font-semibold text-heading mb-5">Agregar meta</h3>

                        <form action="../../logic/goals/create.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                            <div>
                                <label for="subject_id" class="field-label">Materia</label>
                                <select id="subject_id" name="subject_id" required class="input">
                                    <option value="">Selecciona una materia</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= (int) $subject['id'] ?>" <?= (string) $old['subject_id'] === (string) $subject['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="name" class="field-label">Nombre de la meta</label>
                                <input type="text" id="name" name="name" maxlength="100" required placeholder="Ej: Preparar examen final" value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>" class="input">
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="field-label">Descripción</label>
                                <textarea id="description" name="description" rows="3" placeholder="Describe lo que quieres lograr..." class="input"><?= htmlspecialchars($old['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <div>
                                <label for="target_date" class="field-label">Fecha límite</label>
                                <input type="date" id="target_date" name="target_date" value="<?= htmlspecialchars($old['target_date'], ENT_QUOTES, 'UTF-8') ?>" class="input">
                            </div>

                            <div>
                                <label for="status" class="field-label">Estado</label>
                                <select id="status" name="status" class="input">
                                    <?php foreach ($status_labels as $status => $label): ?>
                                        <option value="<?= $status ?>" <?= $old['status'] === $status ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <button type="submit" class="btn btn-primary" style="width: auto;">Agregar meta</button>
                            </div>
                        </form>
                    </section>
                <?php endif; ?>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                        <h3 class="font-display text-lg font-semibold text-heading">Mis metas</h3>
                        <span class="text-xs text-dim"><?= count($goals) ?> registradas</span>
                    </div>

                    <?php if (empty($goals)): ?>
                        <p class="text-sm text-muted">Aún no tienes metas registradas.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-muted border-b" style="border-color: var(--color-border);">
                                        <th class="py-2 pr-4 font-medium">Meta</th>
                                        <th class="py-2 pr-4 font-medium">Materia</th>
                                        <th class="py-2 pr-4 font-medium">Fecha límite</th>
                                        <th class="py-2 pr-4 font-medium">Estado</th>
                                        <th class="py-2 font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($goals as $goal): ?>
                                        <tr class="border-b" style="border-color: var(--color-border);">
                                            <td class="py-3 pr-4 text-heading">
                                                <div class="font-medium"><?= htmlspecialchars($goal['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php if (!empty($goal['description'])): ?>
                                                    <div class="text-xs text-muted mt-1"><?= htmlspecialchars($goal['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 pr-4"><?= htmlspecialchars($goal['subject_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="py-3 pr-4 text-dim"><?= $goal['target_date'] ? htmlspecialchars(date('d/m/Y', strtotime($goal['target_date'])), ENT_QUOTES, 'UTF-8') : 'Sin fecha' ?></td>
                                            <td class="py-3 pr-4"><?= htmlspecialchars($status_labels[$goal['status']] ?? $goal['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="py-3">
                                                <div class="flex items-center gap-2">
                                                    <a href="edit.php?id=<?= (int) $goal['id'] ?>" class="text-sm link-accent">Editar</a>
                                                    <form action="../../logic/goals/delete.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta meta?');">
                                                        <input type="hidden" name="id" value="<?= (int) $goal['id'] ?>">
                                                        <button type="submit" class="btn btn-danger-ghost" style="padding: 0.35rem 0.7rem;">Eliminar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
