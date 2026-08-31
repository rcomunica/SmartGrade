<?php
session_start();
require_once __DIR__ . '/db.php';

$register_path = __DIR__ . '/../views/register.php';
$dashboard_path = '../views/dashboard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $register_path);
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

$errors = [];

// Guardamos lo que el usuario escribió para no obligarlo a retipear todo
$old = [
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
];

if ($first_name === '') {
    $errors[] = 'El nombre es obligatorio.';
}

if ($last_name === '') {
    $errors[] = 'El apellido es obligatorio.';
}

if ($email === '') {
    $errors[] = 'El correo es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo no tiene un formato válido.';
}

if ($password === '') {
    $errors[] = 'La contraseña es obligatoria.';
} elseif (strlen($password) < 8) {
    $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
}

if ($password !== $password_confirm) {
    $errors[] = 'Las contraseñas no coinciden.';
}

// Si ya pasó las validaciones de formato, revisamos si el correo ya existe
if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Ya existe una cuenta registrada con ese correo.';
    }
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = $old;
    header('Location: ' . $register_path);
    exit;
}

// Todo válido: crear el usuario
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    'INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)'
);
$stmt->execute([
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'password' => $hashed,
]);

$userId = $pdo->lastInsertId();

// Login automático tras registrarse
session_regenerate_id(true);
$_SESSION['user_id'] = $userId;
$_SESSION['user_name'] = $first_name . ' ' . $last_name;
$_SESSION['user_email'] = $email;

header('Location: ' . $dashboard_path);
exit;
