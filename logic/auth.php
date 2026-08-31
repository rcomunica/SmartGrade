<?php
session_start();
require_once __DIR__ . '/db.php';

$login_path = '../views/login.php';
$dashboard_path = '../views/dashboard.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validación básica
if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Completa correo y contraseña.';
    $_SESSION['old_email'] = $email;
    header('Location: ' . $login_path);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'El correo no tiene un formato válido.';
    header('Location: ' . $login_path);
    exit;
}

// Buscar usuario
$stmt = $pdo->prepare('SELECT id, first_name, last_name, email, password FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Mensaje genérico a propósito: no reveles si fue el email o el password lo que falló
if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = 'Correo o contraseña incorrectos.';
    $_SESSION['old_email'] = $email;
    header('Location: ' . $login_path);
    exit;
}

// Login correcto: regenerar el ID de sesión (previene session fixation)
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['user_email'] = $user['email'];


// "Recordarme" -> cookie extendida (opcional, básico)
if (!empty($_POST['remember'])) {
    setcookie('remember_email', $user['email'], time() + (86400 * 30), '/');
}

header('Location: ' . $dashboard_path);
exit;
