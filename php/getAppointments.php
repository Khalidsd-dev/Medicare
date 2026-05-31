<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER']) || $_SESSION['LOGGED_IN_USER'] !== true || ($_SESSION['USER_ROLE'] ?? '') !== 'PATIENT') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Please log in as a patient.']);
    exit;
}

require_once 'Executor.php';

try {
    $executorService = new Executor();
    $appointments = $executorService->databaseManager->viewAppointments($_SESSION['USER_ID']);

    if (!$appointments) {
        $appointments = [];
    }

    echo json_encode(['success' => true, 'appointments' => $appointments]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve appointments: ' . $e->getMessage()]);
}
