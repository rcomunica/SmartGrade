<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/terms/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['terms_error'] = 'Periodo inválido.';
    header('Location: ../../views/terms/index.php');
    exit;
}

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
    header('Location: ../../views/terms/edit.php?id=' . (int) $id);
    exit;
}

$current = getUserTermById($pdo, $user_id, (int) $id);
if (!$current) {
    $_SESSION['terms_error'] = 'No se encontró el periodo seleccionado.';
    header('Location: ../../views/terms/index.php');
    exit;
}

$stmt = $pdo->prepare('UPDATE terms SET name = :name WHERE id = :id AND user_id = :user_id');
$stmt->execute([
    'name' => $name,
    'id' => (int) $id,
    'user_id' => $user_id,
]);

unset($_SESSION['terms_old']);
$_SESSION['terms_success'] = $stmt->rowCount() > 0
    ? 'Periodo actualizado correctamente.'
    : 'No hubo cambios para guardar.';

header('Location: ../../views/terms/index.php');
exit;
