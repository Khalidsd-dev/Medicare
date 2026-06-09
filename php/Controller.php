<?php


require_once "Executor.php";

session_start(); // Start the session to manage user authentication and store session variables


// ****** LOGIN FUNCTIONALITY ******

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        header('Location: login.php?error=missing');
        exit();
    }
    $executor = new Executor();
    $user = $executor->login($email, $password);
    

// :(
if (!$user) {
    header('Location: login.php?error=invalid');
    exit();
}

// SUCCESS OPERATION :)
session_regenerate_id(true);

$_SESSION['LOGGED_IN_USER'] = true;
$_SESSION['USER_ROLE'] = $user['user_role'];
$_SESSION['USER_ID'] = $user['user_id'];
$_SESSION['USER_EMAIL'] = $user["email"];
$_SESSION['USER_NAME'] = $user['first_name'];
$_SESSION['USER_SURNAME'] = $user['last_name'];
$_SESSION['USER_INITIALS'] = strtoupper(
    $user['first_name'][0] . $user['last_name'][0]
);

switch ($user['user_role']) {
    case 'PATIENT':
        header('Location: ../view/patientDashboard.php');
        exit();

    case 'DOCTOR':
        header('Location: ../view/doctorDashboard.php');
        exit();

    case 'ADMIN':
        header('Location: ../view/adminDashboard.php');
        exit();

    default:
        header('Location: login.php?error=invalid_role');
        exit();
}

}
