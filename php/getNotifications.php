<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER']) || $_SESSION['LOGGED_IN_USER'] !== true || ($_SESSION['USER_ROLE'] ?? '') !== 'DOCTOR') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Please log in as a doctor.']);
    exit;
}

require_once 'Executor.php';

try {
    $executorService = new Executor();
    $notifications = $executorService->viewNotification();

    echo json_encode(['success' => true, 'messages' => $notifications]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve messages: ' . $e->getMessage()]);
}
?>