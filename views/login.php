<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
$old_email = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_email']);
?>
<!DOCTYPE html>
<html lang="es">


<head>
    <title>Iniciar sesión</title>
    <?php
    include __DIR__ . '/header.php';
    ?>
</head>

<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-4xl grid md:grid-cols-2 card overflow-hidden shadow-elevated">

        <!-- Panel izquierdo: contexto / marca -->
        <div class="hidden md:flex flex-col justify-between card-panel p-10 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl" style="background-color: var(--color-primary-soft);"></div>

            <div class="relative z-10">
                <div class="brand-badge">S</div>
                <h1 class="font-display text-3xl font-bold mt-8 text-heading leading-tight">
                    SmartGrade<br>Gestión académica
                </h1>
                <p class="text-muted mt-3 text-sm max-w-xs">
                    Accede a tu panel de control para gestionar tus calificaciones, cursos y más.
                </p>
            </div>

            <div class="relative z-10 text-xs text-dim font-mono">
                v1.0 — SmartGrade
            </div>
        </div>

        <!-- Panel derecho: formulario -->
        <div class="p-8 sm:p-10 flex flex-col justify-center">
            <h2 class="font-display text-2xl font-semibold text-heading">Bienvenido de nuevo</h2>
            <p class="text-muted text-sm mt-1 mb-6">Ingresa tus credenciales para continuar</p>

            <?php if ($error): ?>
                <div class="alert alert-error mb-5">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="../logic/auth.php" method="POST" class="space-y-4" novalidate>

                <div>
                    <label for="email" class="field-label">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($old_email, ENT_QUOTES, 'UTF-8') ?>"
                        required
                        autocomplete="email"
                        placeholder="tu@correo.com"
                        class="input">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="field-label mb-0">Contraseña</label>
                        <a href="#" class="text-xs link-accent">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="input">
                </div>

                <label class="flex items-center gap-2 text-xs text-muted cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="checkbox">
                    Mantener sesión iniciada
                </label>

                <button type="submit" class="btn btn-primary">
                    Iniciar sesión
                </button>
            </form>

            <p class="text-center text-xs text-dim mt-6">
                ¿No tienes cuenta?
                <a href="register.php" class="link-accent">Regístrate</a>
            </p>
        </div>
    </div>

</body>

</html>