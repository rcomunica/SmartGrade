<?php

if (!function_exists('getUserGoals')) {
    function getUserGoals(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare(
            'SELECT
                g.id,
                g.subject_id,
                g.name,
                g.description,
                g.target_date,
                g.status,
                g.created_at,
                s.name AS subject_name
            FROM goals g
            INNER JOIN subjets s ON s.id = g.subject_id
            WHERE g.user_id = :user_id
              AND s.user_id = :user_id
            ORDER BY
                CASE WHEN g.target_date IS NULL THEN 1 ELSE 0 END,
                g.target_date ASC,
                g.created_at DESC'
        );
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserGoalById')) {
    function getUserGoalById(PDO $pdo, int $user_id, int $id): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT
                g.id,
                g.subject_id,
                g.name,
                g.description,
                g.target_date,
                g.status,
                g.created_at,
                s.name AS subject_name
            FROM goals g
            INNER JOIN subjets s ON s.id = g.subject_id
            WHERE g.id = :id
              AND g.user_id = :user_id
              AND s.user_id = :user_id
            LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'user_id' => $user_id,
        ]);
        $goal = $stmt->fetch();
        return $goal ?: null;
    }
}

if (!function_exists('getUserGoalSubjects')) {
    function getUserGoalSubjects(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare('SELECT id, name FROM subjets WHERE user_id = :user_id ORDER BY name ASC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserGoalBySubjectId')) {
    function getUserGoalBySubjectId(PDO $pdo, int $user_id, int $subject_id): array
    {
        $stmt = $pdo->prepare(
            'SELECT
                g.id,
                g.subject_id,
                g.name,
                g.description,
                g.target_date,
                g.status,
                g.created_at,
                s.name AS subject_name
            FROM goals g
            INNER JOIN subjets s ON s.id = g.subject_id
            WHERE g.subject_id = :subject_id
              AND g.user_id = :user_id
              AND s.user_id = :user_id
            ORDER BY
                CASE WHEN g.target_date IS NULL THEN 1 ELSE 0 END,
                g.target_date ASC,
                g.created_at DESC'
        );
        $stmt->execute([
            'subject_id' => $subject_id,
            'user_id' => $user_id,
        ]);
        return $stmt->fetchAll();
    }
}
