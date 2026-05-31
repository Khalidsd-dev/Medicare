<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER'], $_SESSION['USER_ROLE']) || $_SESSION['LOGGED_IN_USER'] !== true || $_SESSION['USER_ROLE'] !== 'DOCTOR') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$doctorId = $_SESSION['USER_ID'];

try {
    $pdo = require __DIR__ . '/db_connect.php';
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection did not return a PDO instance');
    }

    $stmt = $pdo->prepare(
        'SELECT a.appointment_id, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.appointment_reason, a.appointment_status, a.created_at, a.updated_at, 
                u.first_name AS patient_first_name, u.last_name AS patient_last_name, 
                du.first_name AS doctor_first_name, du.last_name AS doctor_last_name, d.specialization AS doctor_specialty
         FROM appointments a
         JOIN users u ON a.patient_id = u.user_id
         JOIN users du ON a.doctor_id = du.user_id
         JOIN doctors d ON a.doctor_id = d.doctor_id
         WHERE a.doctor_id = ?
         ORDER BY a.appointment_date ASC, a.appointment_time ASC'
    );
    $stmt->execute([$doctorId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['appointments' => $appointments]);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load doctor appointments.']);
}
