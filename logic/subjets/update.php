<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/subjets/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['subjets_error'] = 'Materia inválida.';
    header('Location: ../../views/subjets/index.php');
    exit;
}

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
    header('Location: ../../views/subjets/edit.php?id=' . (int) $id);
    exit;
}

$current = getUserSubjetById($pdo, $user_id, (int) $id);
if (!$current) {
    $_SESSION['subjets_error'] = 'No se encontró la materia seleccionada.';
    header('Location: ../../views/subjets/index.php');
    exit;
}

$stmt = $pdo->prepare('UPDATE subjets SET name = :name, teacher_name = :teacher_name WHERE id = :id AND user_id = :user_id');
$stmt->execute([
    'name' => $name,
    'teacher_name' => $teacher_name,
    'id' => (int) $id,
    'user_id' => $user_id,
]);

unset($_SESSION['subjets_old']);
$_SESSION['subjets_success'] = $stmt->rowCount() > 0
    ? 'Materia actualizada correctamente.'
    : 'No hubo cambios para guardar.';

header('Location: ../../views/subjets/index.php');
exit;
