<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/goals/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['goals_error'] = 'Meta inválida.';
    header('Location: ../../views/goals/index.php');
    exit;
}

$subject_id = filter_var($_POST['subject_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$target_date = trim($_POST['target_date'] ?? '');
$status = $_POST['status'] ?? 'pending';
$errors = [];

if ($subject_id === false) {
    $errors[] = 'Debes seleccionar una materia válida.';
}

if ($name === '') {
    $errors[] = 'El nombre de la meta es obligatorio.';
} elseif (strlen($name) > 100) {
    $errors[] = 'El nombre de la meta no puede superar 100 caracteres.';
}

if (strlen($description) > 65535) {
    $errors[] = 'La descripción de la meta es demasiado extensa.';
}

if ($target_date !== '') {
    $date = DateTime::createFromFormat('!Y-m-d', $target_date);
    if (!$date || $date->format('Y-m-d') !== $target_date) {
        $errors[] = 'La fecha límite no es válida.';
    }
}

$valid_statuses = ['pending', 'in_progress', 'completed'];
if (!in_array($status, $valid_statuses, true)) {
    $errors[] = 'El estado seleccionado no es válido.';
}

$_SESSION['goals_old'] = [
    'subject_id' => $subject_id === false ? '' : $subject_id,
    'name' => $name,
    'description' => $description,
    'target_date' => $target_date,
    'status' => $status,
];

if (!empty($errors)) {
    $_SESSION['goals_error'] = implode(' ', $errors);
    header('Location: ../../views/goals/edit.php?id=' . (int) $id);
    exit;
}

$current = getUserGoalById($pdo, $user_id, (int) $id);
if (!$current) {
    $_SESSION['goals_error'] = 'No se encontró la meta seleccionada.';
    header('Location: ../../views/goals/index.php');
    exit;
}

$subjects = getUserGoalSubjects($pdo, $user_id);
$subject_ids = array_map('intval', array_column($subjects, 'id'));
if (!in_array((int) $subject_id, $subject_ids, true)) {
    $_SESSION['goals_error'] = 'La materia seleccionada no es válida.';
    header('Location: ../../views/goals/edit.php?id=' . (int) $id);
    exit;
}

$stmt = $pdo->prepare(
    'UPDATE goals
     SET subject_id = :subject_id,
         name = :name,
         description = :description,
         target_date = :target_date,
         status = :status
     WHERE id = :id AND user_id = :user_id'
);
$stmt->execute([
    'subject_id' => (int) $subject_id,
    'name' => $name,
    'description' => $description !== '' ? $description : null,
    'target_date' => $target_date !== '' ? $target_date : null,
    'status' => $status,
    'id' => (int) $id,
    'user_id' => $user_id,
]);

unset($_SESSION['goals_old']);
$_SESSION['goals_success'] = $stmt->rowCount() > 0
    ? 'Meta actualizada correctamente.'
    : 'No hubo cambios para guardar.';

header('Location: ../../views/goals/index.php');
exit;
