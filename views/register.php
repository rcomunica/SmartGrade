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

<body class="min-h-screen flex items-center justify-center px-4 py-10 auth-shell auth-register">

    <div class="w-full max-w-4xl grid md:grid-cols-2 card overflow-hidden shadow-elevated auth-card">

        <!-- Panel izquierdo: contexto / marca -->
        <div class="hidden md:flex flex-col justify-between card-panel p-10 relative overflow-hidden auth-aside">

            <!-- Ola superior -->
            <svg class="aside-wave aside-wave-top" viewBox="0 0 400 200" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="gTop" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0" stop-color="#0a8f86"/>
                        <stop offset="1" stop-color="#5cbf44"/>
                    </linearGradient>
                </defs>
                <path d="M0,0 H340 C220,50 100,85 0,128 Z" fill="url(#gTop)"/>
                <path d="M340,0 H400 V12 C330,16 250,40 190,66 C250,30 300,10 340,0 Z" fill="#4db8ff"/>
                <path d="M0,138 C100,92 220,55 345,8" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
            </svg>

            <!-- Ola inferior -->
            <svg class="aside-wave aside-wave-bottom" viewBox="0 0 400 200" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="gBlue" x1="0" y1="1" x2="1" y2="0">
                        <stop offset="0" stop-color="#0a4fa8"/>
                        <stop offset=".55" stop-color="#1a9bd8"/>
                        <stop offset="1" stop-color="#6cc96a"/>
                    </linearGradient>
                    <linearGradient id="gGreen" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0" stop-color="#3fae3f"/>
                        <stop offset="1" stop-color="#1f9a6a"/>
                    </linearGradient>
                </defs>
                <path d="M0,18 C100,92 250,158 400,160 C250,148 100,100 0,52 Z" fill="url(#gGreen)"/>
                <path d="M0,70 C100,125 250,178 400,166 V200 H0 Z" fill="url(#gBlue)"/>
                <path d="M0,60 C100,115 250,170 400,160" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
            </svg>

            <div class="relative z-10">
                <div class="brand-badge">S</div>
                <h1 class="font-display font-bold leading-tight">
                    <span class="t-blue">Crea tu</span><br>
                    <span class="t-green">cuenta</span>
                </h1>
                <p class="aside-text">
                    Regístrate para acceder al sistema del proyecto de grado.
                </p>
            </div>
        </div>

        <!-- Panel derecho: formulario -->
        <div class="p-8 sm:p-10 flex flex-col justify-center auth-form">
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