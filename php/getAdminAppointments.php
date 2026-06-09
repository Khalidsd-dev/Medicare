<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER'], $_SESSION['USER_ROLE']) || $_SESSION['LOGGED_IN_USER'] !== true || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

try {
    $pdo = require __DIR__ . '/db_connect.php';
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection failed.');
    }

    $stmt = $pdo->prepare(
    'SELECT 
        a.appointment_id,
        a.patient_id,
        a.doctor_id,
        a.appointment_date,
        a.appointment_time,
        a.appointment_status,
        a.created_at,
        a.updated_at,

        p.first_name AS patient_first_name,
        p.last_name AS patient_last_name,

        du.first_name AS doctor_first_name,
        du.last_name AS doctor_last_name,

        doc.specialization

     FROM appointments a

     LEFT JOIN users p 
        ON a.patient_id = p.user_id

     LEFT JOIN users du 
        ON a.doctor_id = du.user_id

     LEFT JOIN doctors doc
        ON a.doctor_id = doc.doctor_id

     ORDER BY a.appointment_date DESC,
              a.appointment_time DESC'
);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'appointments' => $appointments
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load appointments.']);
}
