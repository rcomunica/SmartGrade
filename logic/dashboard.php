<?php
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!function_exists('get_avg_grade')) {
    function get_avg_grade(PDO $pdo)
    {
        $stmt = $pdo->prepare(
            'SELECT AVG(subject_average) AS avg_grade
            FROM (
                SELECT
                    g.subjet_id,
                    SUM(g.value * g.percentage) / NULLIF(SUM(g.percentage), 0) AS subject_average
                FROM grades g
                INNER JOIN subjets s ON s.id = g.subjet_id
                INNER JOIN terms t ON t.id = g.term_id
                WHERE g.user_id = :user_id
                  AND s.user_id = :user_id
                  AND t.user_id = :user_id
                  AND t.is_active = 1
                GROUP BY g.subjet_id
            ) AS subject_averages'
        );
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result['avg_grade'] ?? null;
    }
}

if (!function_exists('get_lowest_subject')) {
    function get_lowest_subject(PDO $pdo)
    {
        $stmt = $pdo->prepare('SELECT AVG(g.value) as avg_grade, s.name FROM grades g INNER JOIN subjets s ON s.id = g.subjet_id WHERE g.user_id = :user_id GROUP BY s.id, s.name ORDER BY avg_grade ASC LIMIT 1;');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result ?? null;
    }
}

if (!function_exists('get_high_subject')) {
    function get_high_subject(PDO $pdo)
    {
        $stmt = $pdo->prepare('SELECT AVG(g.value) as avg_grade, s.name FROM grades g INNER JOIN subjets s ON s.id = g.subjet_id WHERE g.user_id = :user_id GROUP BY s.id, s.name ORDER BY avg_grade DESC LIMIT 1;');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result ?? null;
    }
}

if (!function_exists('get_actual_term')) {
    function get_actual_term(PDO $pdo)
    {
        $stmt = $pdo->prepare('SELECT id, name FROM terms WHERE user_id = :user_id AND is_active = 1 LIMIT 1');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result ?? null;
    }
}
