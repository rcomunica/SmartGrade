<?php
session_start();
require_once __DIR__ . '/../../logic/db.php';
require_once __DIR__ . '/../../logic/terms/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    $_SESSION['terms_error'] = 'Periodo inválido.';
    header('Location: index.php');
    exit;
}

$active_page = 'periodos';
$page_title = 'Editar periodo';
$nav_base = '../';
$user_id = (int) $_SESSION['user_id'];

$term = getUserTermById($pdo, $user_id, (int) $id);
if (!$term) {
    $_SESSION['terms_error'] = 'No se encontró el periodo seleccionado.';
    header('Location: index.php');
    exit;
}

$error = $_SESSION['terms_error'] ?? null;
unset($_SESSION['terms_error']);

$old = $_SESSION['terms_old'] ?? null;
unset($_SESSION['terms_old']);

$name_value = $old['name'] ?? $term['name'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar periodo | <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <?php include __DIR__ . '/../header.php'; ?>
</head>

<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="main-content">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>

            <main class="p-6 space-y-6">
                <div>
                    <h2 class="font-display text-xl font-semibold text-heading">Editar periodo</h2>
                    <p class="text-muted text-sm mt-1">Modifica el nombre del periodo seleccionado.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <section class="card p-6">
                    <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
                        <h3 class="font-display text-lg font-semibold text-heading">Actualizar periodo</h3>
                        <a href="index.php" class="text-sm link-accent">Volver al listado</a>
                    </div>

                    <form action="../../logic/terms/update.php" method="POST" class="grid md:grid-cols-2 gap-4" novalidate>
                        <input type="hidden" name="id" value="<?= (int) $term['id'] ?>">

                        <div>
                            <label for="name" class="field-label">Nombre del periodo</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                maxlength="100"
                                required
                                value="<?= htmlspecialchars($name_value, ENT_QUOTES, 'UTF-8') ?>"
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
