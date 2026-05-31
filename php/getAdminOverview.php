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

    $userCounts = [
        'PATIENT' => 0,
        'DOCTOR' => 0,
        'ADMIN' => 0
    ];

    $stmt = $pdo->prepare('SELECT user_role, COUNT(*) AS count FROM users GROUP BY user_role');
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $role = $row['user_role'] ?? '';
        if (array_key_exists($role, $userCounts)) {
            $userCounts[$role] = (int)$row['count'];
        }
    }

    $appointmentCounts = [
        'REQUESTED' => 0,
        'CONFIRMED' => 0,
        'COMPLETED' => 0,
        'CANCELLED' => 0
    ];

    $stmt = $pdo->prepare('SELECT appointment_status, COUNT(*) AS count FROM appointments GROUP BY appointment_status');
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['appointment_status'] ?? '';
        if (array_key_exists($status, $appointmentCounts)) {
            $appointmentCounts[$status] = (int)$row['count'];
        }
    }

    echo json_encode([
        'success' => true,
        'user_counts' => $userCounts,
        'appointment_counts' => $appointmentCounts
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load admin overview.']);
}
