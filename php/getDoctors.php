<?php
header('Content-Type: application/json');

try {
    $pdo = require __DIR__ . '/db_connect.php';

    // Join doctors with users to obtain display names and specialties
    $stmt = $pdo->prepare(
        'SELECT d.doctor_id, d.specialization, d.doctor_phone, d.doctor_status, u.first_name, u.last_name
         FROM doctors d
         LEFT JOIN users u ON d.doctor_id = u.user_id'
    );

    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['doctors' => $doctors]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load doctors: ' . $e->getMessage()]);
}

?>