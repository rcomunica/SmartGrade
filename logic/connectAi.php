<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/OpenAi/openAi.php';
require_once __DIR__ . '/grades/read.php';
require_once __DIR__ . '/db.php';



if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

$grades = getUserGrades($pdo, (int) $_SESSION['user_id']);
$formattedGradesJson = formatGradesJson($pdo, $grades);
// var_dump($formattedGradesJson);
$result = callOpenAi($apiKey, $formattedGradesJson);
$Parsedown = new Parsedown();
$parsedResult = $Parsedown->text($result);

$_SESSION['ai_result'] = $parsedResult;

header('Location: ../../views/dashboard.php');
exit;
