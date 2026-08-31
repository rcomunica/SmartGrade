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
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['subjets_error'] = 'Materia inválida.';
    header('Location: ../../views/subjets/index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM subjets WHERE id = :id AND user_id = :user_id');
$stmt->execute([
    'id' => (int) $id,
    'user_id' => $user_id,
]);

$_SESSION['subjets_success'] = $stmt->rowCount() > 0
    ? 'Materia eliminada correctamente.'
    : 'No se encontró la materia seleccionada.';

header('Location: ../../views/subjets/index.php');
exit;
