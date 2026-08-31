<?php
// config/db.php
// Ajusta estos datos a tu entorno (XAMPP/local o servidor)

$DB_HOST = 'localhost';
$DB_NAME = 'smart';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // En producción no muestres el error crudo, esto es solo para dev
    die('Error de conexión: ' . $e->getMessage());
}
