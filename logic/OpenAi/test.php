<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/openAi.php';


$test_file = file_get_contents(__DIR__ . '/test.json');
// USO:
$resultado = callOpenAi($apiKey, $test_file);

var_dump($resultado);
