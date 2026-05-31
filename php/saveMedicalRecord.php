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
$patientId = isset($payload['patient_id']) ? filter_var($payload['patient_id'], FILTER_VALIDATE_INT) : null;
$diagnosis = trim($payload['diagnosis'] ?? '');
$prescription = trim($payload['prescription'] ?? '');
$treatmentNotes = trim($payload['treatment_notes'] ?? '');

if (!$appointmentId || !$patientId) {
    http_response_code(400);
    echo json_encode(['error' => 'Appointment and patient selection are required.']);
    exit;
}

try {
    $pdo = require __DIR__ . '/db_connect.php';
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection did not return a PDO instance');
    }

    // Verify appointment belongs to this doctor and patient
    $stmt = $pdo->prepare('SELECT appointment_id, patient_id, doctor_id, appointment_status FROM appointments WHERE appointment_id = ? AND doctor_id = ?');
    $stmt->execute([$appointmentId, $_SESSION['USER_ID']]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment || (int)$appointment['patient_id'] !== $patientId) {
        http_response_code(403);
        echo json_encode(['error' => 'Appointment not found or you are not authorized to modify it.']);
        exit;
    }

    if ($appointment['appointment_status'] === 'COMPLETED') {
        http_response_code(400);
        echo json_encode(['error' => 'This appointment has already been completed.']);
        exit;
    }

    $insert = $pdo->prepare(
        'INSERT INTO medical_records (patient_id, doctor_id, appointment_id, diagnosis, prescription, treatment_notes) VALUES (?, ?, ?, ?, ?, ?)'
    );

    $insert->execute([
        $patientId,
        $_SESSION['USER_ID'],
        $appointmentId,
        $diagnosis,
        $prescription,
        $treatmentNotes
    ]);

    $update = $pdo->prepare('UPDATE appointments SET appointment_status = ?, updated_at = NOW() WHERE appointment_id = ? AND doctor_id = ?');
    $update->execute(['COMPLETED', $appointmentId, $_SESSION['USER_ID']]);

    echo json_encode([
        'success' => true,
        'message' => 'Medical record saved and appointment marked as completed.',
        'record_id' => $pdo->lastInsertId()
    ]);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save medical record: ' . $th->getMessage()]);
}
