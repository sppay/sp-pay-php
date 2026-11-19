<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'object' => 'echo',
    'echo' => [
        'path' => $path,
        'authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        'body' => json_decode(file_get_contents('php://input'), true),
    ],
]);
