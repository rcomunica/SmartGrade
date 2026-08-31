<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/grades/index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    $_SESSION['grades_error'] = 'Nota inválida.';
    header('Location: ../../views/grades/index.php');
    exit;
}

$stmt = $pdo->prepare(
    'DELETE g
     FROM grades g
     INNER JOIN subjets s ON s.id = g.subjet_id
     INNER JOIN terms t ON t.id = g.term_id
     WHERE g.id = :id
       AND s.user_id = :user_id
       AND t.user_id = :user_id'
);
$stmt->execute([
    'id' => (int) $id,
    'user_id' => $user_id,
]);

$_SESSION['grades_success'] = $stmt->rowCount() > 0
    ? 'Nota eliminada correctamente.'
    : 'No se encontró la nota seleccionada.';

header('Location: ../../views/grades/index.php');
exit;
