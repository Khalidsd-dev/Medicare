<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER'], $_SESSION['USER_ROLE']) || $_SESSION['LOGGED_IN_USER'] !== true || $_SESSION['USER_ROLE'] !== 'DOCTOR') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$appointmentId = isset($payload['appointment_id']) ? filter_var($payload['appointment_id'], FILTER_VALIDATE_INT) : null;
$status = isset($payload['status']) ? strtoupper(trim($payload['status'])) : null;

$allowedStatuses = ['CONFIRMED', 'CANCELLED', 'COMPLETED'];
if (!$appointmentId || !$status || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid appointment status request.']);
    exit;
}

$doctorId = $_SESSION['USER_ID'];

try {
    $pdo = require __DIR__ . '/db_connect.php';
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection did not return a PDO instance');
    }

    $stmt = $pdo->prepare(
        'UPDATE appointments SET appointment_status = ?, updated_at = NOW() WHERE appointment_id = ? AND doctor_id = ?'
    );
    $stmt->execute([$status, $appointmentId, $doctorId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Appointment not found or you are not authorized to update it.']);
        exit;
    }

    echo json_encode(['message' => 'Appointment status updated successfully.']);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to update appointment status.']);
}
