<?php
session_start();
$login_path = '../views/login.php';

$_SESSION = [];
session_destroy();
setcookie('remember_email', '', time() - 3600, '/');
header('Location: ' . $login_path);
exit;
