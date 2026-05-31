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

    $executorService = new Executor();
    $loginResult = $executorService->loginASPatient($email, $password);
    $doctorResult = $executorService->loginASDoctor($email, $password);
    $adminLogin = $executorService->loginASAdmin($email, $password);

    

// if loginresult fetch array contains email and password that match the input, then login successful, else login failed

   if ($loginResult) {

    session_regenerate_id(true);

    $Initials = $initialsData['initials'] ?? strtoupper(
            $loginResult['first_name'][0] . $loginResult['last_name'][0]
        );

    $_SESSION['LOGGED_IN_USER'] = true;
    $_SESSION['USER_ROLE'] = $loginResult['user_role'];
    $_SESSION['USER_ID'] = $loginResult['user_id'];
    $_SESSION['USER_EMAIL'] = $loginResult["email"];
    $_SESSION['USER_NAME'] = $loginResult['first_name'];
    $_SESSION['USER_SURNAME'] = $loginResult['last_name'];
    $_SESSION["USER_INITIALS"] = $Initials;
    header("location: ../view/patientDashboard.php");
    exit();
} 

if ($doctorResult) {
    session_regenerate_id(true);

    $Initials = $initialsData['initials'] ?? strtoupper(
            $doctorResult['first_name'][0] . $doctorResult['last_name'][0]
        );

    $_SESSION['LOGGED_IN_USER'] = true;
    $_SESSION['USER_ROLE'] = $doctorResult['user_role'];
    $_SESSION['USER_ID'] = $doctorResult['user_id'];
    $_SESSION['USER_EMAIL'] = $doctorResult["email"];
    $_SESSION['USER_NAME'] = $doctorResult['first_name'];
    $_SESSION['USER_SURNAME'] = $doctorResult['last_name'];
    $_SESSION["USER_INITIALS"] = $Initials;
    
    header("location: ../view/doctorDashboard.php");
    exit();
}


if ($adminLogin) {
     session_regenerate_id(true);

    $_SESSION['LOGGED_IN_USER'] = true;
    $_SESSION['USER_ROLE'] = $adminLogin['user_role'];
    $_SESSION['USER_ID'] = $adminLogin['user_id'];
    $_SESSION['USER_EMAIL'] = $adminLogin["email"];
    $_SESSION['USER_NAME'] = $adminLogin['first_name'];
    $_SESSION['USER_SURNAME'] = $adminLogin['last_name'];
    $_SESSION["USER_INITIALS"] = $adminLogin['initials'];

    header("location: ../view/adminDashboard.php");
    exit();
}

header("Location: login.php?error=invalid");
exit();
}

// ****** END OF LOGIN FUNCTIONALITY ******


?>