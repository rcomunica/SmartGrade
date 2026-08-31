<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/subjets/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$active_page = 'materias';
$page_title = 'Mis materias';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$success = $_SESSION['subjets_success'] ?? null;
unset($_SESSION['subjets_success']);

$error = $_SESSION['subjets_error'] ?? null;
unset($_SESSION['subjets_error']);

$old = $_SESSION['subjets_old'] ?? ['name' => '', 'teacher_name' => ''];
unset($_SESSION['subjets_old']);

$subjets = getUserSubjets($pdo, $user_id);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Mis materias | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Gestión de materias</h2>
                    <p class="text-muted text-sm mt-1">Agrega, edita y elimina tus materias registradas.</p>
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

                <section class="card p-6">
                    <h3 class="font-display text-lg font-semibold text-heading mb-5">Agregar materia</h3>

                    <form action="../../logic/subjets/create.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                        <div>
                            <label for="name" class="field-label">Nombre de la materia</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                maxlength="100"
                                required
                                placeholder="Ej: Matemáticas"
                                value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>"
                                class="input">
                        </div>

                        <div>
                            <label for="teacher_name" class="field-label">Nombre del docente</label>
                            <input
                                type="text"
                                id="teacher_name"
                                name="teacher_name"
                                maxlength="100"
                                required
                                placeholder="Ej: Ana Rodríguez"
                                value="<?= htmlspecialchars($old['teacher_name'], ENT_QUOTES, 'UTF-8') ?>"
                                class="input">
                        </div>

                        <div class="md:col-span-2">
                            <button type="submit" class="btn btn-primary" style="width: auto;">Agregar materia</button>
                        </div>
                    </form>
                </section>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                        <h3 class="font-display text-lg font-semibold text-heading">Mis materias</h3>
                        <span class="text-xs text-dim"><?= count($subjets) ?> registradas</span>
                    </div>

                    <?php if (empty($subjets)): ?>
                        <p class="text-sm text-muted">Aún no tienes materias registradas.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-muted border-b" style="border-color: var(--color-border);">
                                        <th class="py-2 pr-4 font-medium">Materia</th>
                                        <th class="py-2 pr-4 font-medium">Docente</th>
                                        <th class="py-2 pr-4 font-medium">Fecha de registro</th>
                                        <th class="py-2 font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjets as $subjet): ?>
                                        <tr class="border-b" style="border-color: var(--color-border);">
                                            <td class="py-3 pr-4 text-heading"><?= htmlspecialchars($subjet['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="py-3 pr-4"><?= htmlspecialchars($subjet['teacher_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="py-3 pr-4 text-dim"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($subjet['created_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="py-3">
                                                <div class="flex items-center gap-2">
                                                    <a href="edit.php?id=<?= (int) $subjet['id'] ?>" class="text-sm link-accent">Editar</a>
                                                    <form action="../../logic/subjets/delete.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta materia?');">
                                                        <input type="hidden" name="id" value="<?= (int) $subjet['id'] ?>">
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