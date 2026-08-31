<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/terms/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$errors = [];

if ($name === '') {
    $errors[] = 'El nombre del periodo es obligatorio.';
} elseif (strlen($name) > 100) {
    $errors[] = 'El nombre del periodo no puede superar 100 caracteres.';
}

$_SESSION['terms_old'] = [
    'name' => $name,
];

if (!empty($errors)) {
    $_SESSION['terms_error'] = implode(' ', $errors);
    header('Location: ../../views/terms/index.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO terms (name, user_id) VALUES (:name, :user_id)');
$stmt->execute([
    'name' => $name,
    'user_id' => $user_id,
]);

unset($_SESSION['terms_old']);
$_SESSION['terms_success'] = 'Periodo agregado correctamente.';
header('Location: ../../views/terms/index.php');
exit;
