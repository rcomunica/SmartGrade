<?php

if (!function_exists('getUserSubjetsOptions')) {
    function getUserSubjetsOptions(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare('SELECT id, name FROM subjets WHERE user_id = :user_id ORDER BY name ASC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserTermsOptions')) {
    function getUserTermsOptions(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare('SELECT id, name FROM terms WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserGrades')) {
    function getUserGrades(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare(
            'SELECT
                g.id,
                g.subjet_id,
                g.term_id,
                g.name,
                g.value,
                avg(g.value) OVER (PARTITION BY g.subjet_id, g.term_id) AS avg_grade,
                g.percentage,
                g.created_at,
                s.name AS subjet_name,
                t.name AS term_name
            FROM grades g
            INNER JOIN subjets s ON s.id = g.subjet_id
            INNER JOIN terms t ON t.id = g.term_id
            WHERE s.user_id = :user_id
              AND t.user_id = :user_id
            ORDER BY s.name ASC, g.created_at DESC'
        );
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserGradeById')) {
    function getUserGradeById(PDO $pdo, int $user_id, int $grade_id): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT
                g.id,
                g.subjet_id,
                g.term_id,
                g.name,
                g.value,
                g.percentage,
                g.created_at
            FROM grades g
            INNER JOIN subjets s ON s.id = g.subjet_id
            INNER JOIN terms t ON t.id = g.term_id
            WHERE g.id = :grade_id
              AND s.user_id = :user_id
              AND t.user_id = :user_id
            LIMIT 1'
        );
        $stmt->execute([
            'grade_id' => $grade_id,
            'user_id' => $user_id,
        ]);
        $grade = $stmt->fetch();
        return $grade ?: null;
    }
}

if (!function_exists('getGradePercentageTotal')) {
    function getGradePercentageTotal(PDO $pdo, int $user_id, int $subjet_id, int $term_id, ?int $exclude_grade_id = null): float
    {
        $query = 'SELECT COALESCE(SUM(g.percentage), 0) AS total
            FROM grades g
            INNER JOIN subjets s ON s.id = g.subjet_id
            INNER JOIN terms t ON t.id = g.term_id
            WHERE g.subjet_id = :subjet_id
              AND g.term_id = :term_id
              AND s.user_id = :user_id
              AND t.user_id = :user_id';

        $params = [
            'subjet_id' => $subjet_id,
            'term_id' => $term_id,
            'user_id' => $user_id,
        ];

        if ($exclude_grade_id !== null) {
            $query .= ' AND g.id <> :exclude_grade_id';
            $params['exclude_grade_id'] = $exclude_grade_id;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (float) ($row['total'] ?? 0);
    }
}
