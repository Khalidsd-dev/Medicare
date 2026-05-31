<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER']) || $_SESSION['LOGGED_IN_USER'] !== true || ($_SESSION['USER_ROLE'] ?? '') !== 'PATIENT') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Please log in as a patient to book an appointment.']);
    exit;
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);

if (!$payload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request payload.']);
    exit;
}

$doctorId = filter_var($payload['doctor_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
$appointmentDate = trim($payload['appointment_date'] ?? '');
$appointmentTime = trim($payload['appointment_time'] ?? '');
$doctorName = trim($payload['doctor_name'] ?? '');
$doctorSpecialty = trim($payload['doctor_specialty'] ?? '');

if (!$doctorId || !$appointmentDate || !$appointmentTime) {
    http_response_code(400);
    echo json_encode(['error' => 'Doctor, date, and time are required.']);
    exit;
}

try {
    $pdo = require __DIR__ . '/db_connect.php';

    // Verify doctor exists to avoid FK constraint failure
    $checkDoctor = $pdo->prepare('SELECT doctor_id FROM doctors WHERE doctor_id = ?');
    $checkDoctor->execute([$doctorId]);
    $doctorRow = $checkDoctor->fetch(PDO::FETCH_ASSOC);

    if (!$doctorRow) {
        http_response_code(400);
        echo json_encode(['error' => 'Selected doctor not found. Please choose a valid doctor.']);
        exit;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, appointment_status) VALUES (?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $_SESSION['USER_ID'],
        $doctorId,
        $appointmentDate,
        $appointmentTime,
        'REQUESTED'
    ]);

    $appointmentId = $pdo->lastInsertId();

    $select = $pdo->prepare('SELECT appointment_id, patient_id, doctor_id, appointment_date, appointment_time, appointment_status, created_at, updated_at FROM appointments WHERE appointment_id = ?');
    $select->execute([$appointmentId]);
    $appointment = $select->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Appointment booked successfully.',
        'appointment' => $appointment
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to book appointment: ' . $e->getMessage()]);
}
