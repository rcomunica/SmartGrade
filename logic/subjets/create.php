<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/subjets/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$teacher_name = trim($_POST['teacher_name'] ?? '');
$errors = [];

if ($name === '') {
    $errors[] = 'El nombre de la materia es obligatorio.';
} elseif (strlen($name) > 100) {
    $errors[] = 'El nombre de la materia no puede superar 100 caracteres.';
}

if ($teacher_name === '') {
    $errors[] = 'El nombre del docente es obligatorio.';
} elseif (strlen($teacher_name) > 100) {
    $errors[] = 'El nombre del docente no puede superar 100 caracteres.';
}

$_SESSION['subjets_old'] = [
    'name' => $name,
    'teacher_name' => $teacher_name,
];

if (!empty($errors)) {
    $_SESSION['subjets_error'] = implode(' ', $errors);
    header('Location: ../../views/subjets/index.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO subjets (name, user_id, teacher_name) VALUES (:name, :user_id, :teacher_name)');
$stmt->execute([
    'name' => $name,
    'user_id' => $user_id,
    'teacher_name' => $teacher_name,
]);

unset($_SESSION['subjets_old']);
$_SESSION['subjets_success'] = 'Materia agregada correctamente.';
header('Location: ../../views/subjets/index.php');
exit;
