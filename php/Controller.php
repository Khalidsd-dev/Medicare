<?php


require_once "Executor.php";

session_start(); // Start the session to manage user authentication and store session variables


// ****** LOGIN FUNCTIONALITY ******

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email']; // Use null coalescing operator to handle undefined index
    $password = $_POST['password']; // Use null coalescing operator to handle undefined index
    
    $executorService = new Executor();
    $loginResult = $executorService->loginASPatient($email, $password);
    $Initials = $initialsData['initials'] ?? strtoupper(
            $loginResult['first_name'][0] . $loginResult['last_name'][0]
        );
// if loginresult fetch array contains email and password that match the input, then login successful, else login failed

   if ($loginResult) {

    session_regenerate_id();

    $_SESSION['LOGGED_IN_USER'] = true;
    $_SESSION['USER_ID'] = $loginResult['user_id'];
    $_SESSION['USER_EMAIL'] = $loginResult["email"];
    $_SESSION['USER_NAME'] = $loginResult['first_name'];
    $_SESSION['USER_SURNAME'] = $loginResult['last_name'];
    $_SESSION["USER_INITIALS"] = $Initials
    ;
    header("location: ../view/patientDashboard.html");
    exit();

} else {
    header("Location: login.php?error=invalid");
    exit();
}
} else {

    // Missing email or password, redirect back to the login page with an error message 
    header("Location: login.php?error=missing");
    exit();
}


// ****** END OF LOGIN FUNCTIONALITY ******











?>