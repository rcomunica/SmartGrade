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
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['terms_error'] = 'Periodo inválido.';
    header('Location: ../../views/terms/index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM terms WHERE id = :id AND user_id = :user_id');
$stmt->execute([
    'id' => (int) $id,
    'user_id' => $user_id,
]);

$_SESSION['terms_success'] = $stmt->rowCount() > 0
    ? 'Periodo eliminado correctamente.'
    : 'No se encontró el periodo seleccionado.';

header('Location: ../../views/terms/index.php');
exit;
