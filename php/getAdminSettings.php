<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER'], $_SESSION['USER_ROLE']) || $_SESSION['LOGGED_IN_USER'] !== true || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$envPath = __DIR__ . '/../env/config.env';
if (!file_exists($envPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration file not found.']);
    exit;
}

$data = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$settings = [];
foreach ($data as $line) {
    if (strpos(trim($line), '#') === 0) {
        continue;
    }
    [$key, $value] = array_map('trim', explode('=', $line, 2) + ['', '']);
    if ($key === '' || $value === '') {
        continue;
    }

    if ($key === 'DB_PASS') {
        continue;
    }

    $settings[$key] = $value;
}

echo json_encode([
    'success' => true,
    'settings' => $settings
]);
