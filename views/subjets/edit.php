<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/subjets/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    $_SESSION['subjets_error'] = 'Materia inválida.';
    header('Location: index.php');
    exit;
}

$active_page = 'materias';
$page_title = 'Editar materia';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$subjet = getUserSubjetById($pdo, $user_id, (int) $id);
if (!$subjet) {
    $_SESSION['subjets_error'] = 'No se encontró la materia seleccionada.';
    header('Location: index.php');
    exit;
}

$error = $_SESSION['subjets_error'] ?? null;
unset($_SESSION['subjets_error']);

$old = $_SESSION['subjets_old'] ?? null;
unset($_SESSION['subjets_old']);

$name_value = $old['name'] ?? $subjet['name'];
$teacher_value = $old['teacher_name'] ?? $subjet['teacher_name'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar materia | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Editar materia</h2>
                    <p class="text-muted text-sm mt-1">Modifica los datos de la materia seleccionada.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
                        <h3 class="font-display text-lg font-semibold text-heading">Actualizar materia</h3>
                        <a href="index.php" class="text-sm link-accent">Volver al listado</a>
                    </div>

                    <form action="../../logic/subjets/update.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                        <input type="hidden" name="id" value="<?= (int) $subjet['id'] ?>">

                        <div>
                            <label for="name" class="field-label">Nombre de la materia</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                maxlength="100"
                                required
                                value="<?= htmlspecialchars($name_value, ENT_QUOTES, 'UTF-8') ?>"
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
                                value="<?= htmlspecialchars($teacher_value, ENT_QUOTES, 'UTF-8') ?>"
                                class="input">
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
