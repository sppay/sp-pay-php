<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

header('Content-Type: application/json');

if ($path === '/maintenance/v1/api/transfers/validate-account') {
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => 'SPPay is undergoing maintenance.', 'code' => 'MAINTENANCE']);

    return;
}

echo json_encode([
    'status' => 'success',
    'object' => 'echo',
    'echo' => [
        'path' => $path,
        'authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        'body' => json_decode(file_get_contents('php://input'), true),
    ],
]);
