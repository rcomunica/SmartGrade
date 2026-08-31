<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = $_SESSION['register_errors'] ?? [];
unset($_SESSION['register_errors']);
$old = $_SESSION['register_old'] ?? [];
unset($_SESSION['register_old']);

function old($field, $old)
{
    return htmlspecialchars($old[$field] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php
    include __DIR__ . '/header.php';
    ?>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-4xl grid md:grid-cols-2 card overflow-hidden shadow-elevated">

        <!-- Panel izquierdo: contexto / marca -->
        <div class="hidden md:flex flex-col justify-between card-panel p-10 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl" style="background-color: var(--color-primary-soft);"></div>

            <div class="relative z-10">
                <div class="brand-badge">P</div>
                <h1 class="font-display text-3xl font-bold mt-8 text-heading leading-tight">
                    Crea tu<br>cuenta
                </h1>
                <p class="text-muted mt-3 text-sm max-w-xs">
                    Regístrate para acceder al sistema del proyecto de grado.
                </p>
            </div>

            <div class="relative z-10 text-xs text-dim font-mono">
                v1.0 — auth module
            </div>
        </div>

        <!-- Panel derecho: formulario -->
        <div class="p-8 sm:p-10 flex flex-col justify-center">
            <h2 class="font-display text-2xl font-semibold text-heading">Regístrate</h2>
            <p class="text-muted text-sm mt-1 mb-6">Completa tus datos para crear una cuenta</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error mb-5">
                    <ul class="list-disc list-inside space-y-0.5">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="../logic/register.php" method="POST" class="space-y-4" novalidate>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="field-label">Nombre</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= old('first_name', $old) ?>"
                            required
                            autocomplete="given-name"
                            placeholder="Juan"
                            class="input">
                    </div>
                    <div>
                        <label for="last_name" class="field-label">Apellido</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= old('last_name', $old) ?>"
                            required
                            autocomplete="family-name"
                            placeholder="Pérez"
                            class="input">
                    </div>
                </div>

                <div>
                    <label for="email" class="field-label">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= old('email', $old) ?>"
                        required
                        autocomplete="email"
                        placeholder="tu@correo.com"
                        class="input">
                </div>

                <div>
                    <label for="password" class="field-label">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Mínimo 8 caracteres"
                        class="input">
                </div>

                <div>
                    <label for="password_confirm" class="field-label">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        required
                        autocomplete="new-password"
                        placeholder="Repite tu contraseña"
                        class="input">
                </div>

                <button type="submit" class="btn btn-primary">
                    Crear cuenta
                </button>
            </form>

            <p class="text-center text-xs text-dim mt-6">
                ¿Ya tienes cuenta?
                <a href="login.php" class="link-accent">Inicia sesión</a>
            </p>
        </div>
    </div>

</body>

</html>