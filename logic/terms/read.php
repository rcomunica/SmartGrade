<?php

if (!function_exists('getUserTerms')) {
    function getUserTerms(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare('SELECT id, name, created_at FROM terms WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserTermById')) {
    function getUserTermById(PDO $pdo, int $user_id, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT id, name, created_at FROM terms WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute([
            'id' => $id,
            'user_id' => $user_id,
        ]);
        $term = $stmt->fetch();
        return $term ?: null;
    }
}
