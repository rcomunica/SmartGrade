<?php

if (!function_exists('getUserSubjets')) {
    function getUserSubjets(PDO $pdo, int $user_id): array
    {
        $stmt = $pdo->prepare('SELECT id, name, teacher_name, created_at FROM subjets WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getUserSubjetById')) {
    function getUserSubjetById(PDO $pdo, int $user_id, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT id, name, teacher_name, created_at FROM subjets WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute([
            'id' => $id,
            'user_id' => $user_id,
        ]);
        $subjet = $stmt->fetch();
        return $subjet ?: null;
    }
}
