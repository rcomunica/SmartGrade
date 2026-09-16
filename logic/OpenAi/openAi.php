<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../dashboard.php';
require_once __DIR__ . '/../goals/read.php';


function formatGradesJson(PDO $pdo, array $grades)
{
    $term = get_actual_term($pdo);
    $avgGeneral = get_avg_grade($pdo);

    $materias = [];
    foreach ($grades as $grade) {
        $subjectId = $grade['subjet_id'];

        if (!isset($materias[$subjectId])) {
            // se ejecuta UNA sola vez por materia, no por cada nota
            $metasRaw = getUserGoalBySubjectId($pdo, $_SESSION['user_id'], $subjectId);

            $metas = [];
            foreach ($metasRaw as $meta) {
                $metas[] = [
                    'nombre'         => $meta['name'],
                    'descripcion'    => $meta['description'],
                    'fecha_objetivo' => $meta['target_date'],
                    'estado'         => $meta['status'],
                ];
            }

            $materias[$subjectId] = [
                'nombre'           => $grade['subjet_name'],
                'promedio_actual'  => $grade['avg_grade'] ? round($grade['avg_grade'], 2) : 'N/A',
                'objetivo'         => '',
                'notas'            => [],
                'metas'            => $metas,
                '_peso_acumulado'  => 0,
            ];
        }

        $materias[$subjectId]['notas'][] = [
            'nombre' => $grade['name'],
            'nota'   => $grade['value'],
            'peso'   => $grade['percentage'] . '%',
        ];

        $materias[$subjectId]['_peso_acumulado'] += (float) $grade['percentage'];
    }

    foreach ($materias as &$materia) {
        $materia['peso_pendiente'] = round(100 - $materia['_peso_acumulado'], 2) . '%';
        unset($materia['_peso_acumulado']);
    }
    unset($materia);

    $formatted = [
        'estudiante'       => $_SESSION['user_name'],
        'periodo'          => $term['name'] ?? 'N/A',
        'promedio_general' => $avgGeneral ? round($avgGeneral, 2) : 'N/A',
        'meta_promedio'    => 'N/A',
        'materias'         => array_values($materias),
    ];

    return json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function callOpenAi(string $apiKey, string $userInput)
{
    $instructions = file_get_contents(__DIR__ . '/description.md');
    if ($instructions === false) {
        throw new RuntimeException('No pude leer archivo.md');
    }
    $data = [
        "model" => "gpt-5-mini-2025-08-07",
        "messages" => [
            [
                "role" => "system",
                "content" => $instructions
            ],
            [
                "role" => "user",
                "content" => $userInput
            ]
        ],
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $respuesta = curl_exec($ch);
    curl_close($ch);

    // Decodificar y extraer el texto de respuesta
    $json = json_decode($respuesta, true);
    return $json['choices'][0]['message']['content'] ?? 'No se recibió respuesta.';
}
