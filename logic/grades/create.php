<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../subjets/read.php';
require_once __DIR__ . '/../terms/read.php';
require_once __DIR__ . '/read.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/grades/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$subjet_id = filter_var($_POST['subjet_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$term_id = filter_var($_POST['term_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim($_POST['name'] ?? '');
$value_raw = trim((string) ($_POST['value'] ?? ''));
$percentage_raw = trim((string) ($_POST['percentage'] ?? ''));
$errors = [];

$_SESSION['grades_old'] = [
    'subjet_id' => $subjet_id !== false ? (string) $subjet_id : '',
    'term_id' => $term_id !== false ? (string) $term_id : '',
    'name' => $name,
    'value' => $value_raw,
    'percentage' => $percentage_raw,
];

if ($subjet_id === false) {
    $errors[] = 'La materia seleccionada no es válida.';
}

if ($term_id === false) {
    $errors[] = 'El periodo seleccionado no es válido.';
}

if ($name === '') {
    $errors[] = 'El nombre de la nota es obligatorio.';
} elseif (strlen($name) > 100) {
    $errors[] = 'El nombre de la nota no puede superar 100 caracteres.';
}

if ($value_raw === '' || !is_numeric($value_raw)) {
    $errors[] = 'El valor de la nota debe ser numérico.';
} else {
    $value = (float) $value_raw;
    if ($value < 1 || $value > 5) {
        $errors[] = 'El valor de la nota debe estar entre 1.0 y 5.0.';
    }
}

if ($percentage_raw === '' || !is_numeric($percentage_raw)) {
    $errors[] = 'El porcentaje debe ser numérico.';
} else {
    $percentage = (float) $percentage_raw;
    if ($percentage <= 0 || $percentage > 100) {
        $errors[] = 'El porcentaje debe ser mayor a 0 y menor o igual a 100.';
    }
}

if ($subjet_id !== false && !getUserSubjetById($pdo, $user_id, (int) $subjet_id)) {
    $errors[] = 'La materia seleccionada no pertenece a tu cuenta.';
}

if ($term_id !== false && !getUserTermById($pdo, $user_id, (int) $term_id)) {
    $errors[] = 'El periodo seleccionado no pertenece a tu cuenta.';
}

if (empty($errors) && isset($percentage)) {
    $current_total = getGradePercentageTotal($pdo, $user_id, (int) $subjet_id, (int) $term_id);
    if (($current_total + $percentage) > 100.0001) {
        $errors[] = 'La suma de porcentajes para esa materia y periodo no puede superar 100%.';
    }
}

if (!empty($errors)) {
    $_SESSION['grades_error'] = implode(' ', $errors);
    header('Location: ../../views/grades/index.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO grades (subjet_id, term_id, name, user_id, value, percentage) VALUES (:subjet_id, :term_id, :name, :user_id, :value, :percentage)');
$stmt->execute([
    'subjet_id' => (int) $subjet_id,
    'term_id' => (int) $term_id,
    'name' => $name,
    'user_id' => $user_id,
    'value' => (float) $value,
    'percentage' => (float) $percentage,
]);

unset($_SESSION['grades_old']);
$_SESSION['grades_success'] = 'Nota agregada correctamente.';
header('Location: ../../views/grades/index.php');
exit;
