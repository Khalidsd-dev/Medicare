<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['LOGGED_IN_USER'], $_SESSION['USER_ROLE']) || $_SESSION['LOGGED_IN_USER'] !== true || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request payload.']);
    exit;
}

$firstName = trim($payload['first_name'] ?? '');
$lastName = trim($payload['last_name'] ?? '');
$email = filter_var($payload['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = trim($payload['password'] ?? '');
$role = strtoupper(trim($payload['role'] ?? 'PATIENT'));
$gender = strtoupper(trim($payload['gender'] ?? 'OTHER'));
$accountStatus = strtoupper(trim($payload['account_status'] ?? 'ACTIVE'));

$validRoles = ['PATIENT', 'DOCTOR', 'ADMIN'];
$validGenders = ['MALE', 'FEMALE', 'OTHER'];
$validStatuses = ['ACTIVE', 'INACTIVE', 'SUSPENDED'];

if (!$firstName || !$lastName || !$email || !$password || !in_array($role, $validRoles, true) || !in_array($gender, $validGenders, true) || !in_array($accountStatus, $validStatuses, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid user fields.']);
    exit;
}

try {
    $pdo = require __DIR__ . '/db_connect.php';
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection did not return a PDO instance');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        'INSERT INTO users (first_name, last_name, email, password, gender, user_role, account_status) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $gender, $role, $accountStatus]);

    echo json_encode(['success' => true, 'message' => 'User created successfully.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to create user: ' . $e->getMessage()]);
}
