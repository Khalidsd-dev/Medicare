<?php


require_once "Executor.php";

session_start(); // Start the session to manage user authentication and store session variables


// ****** LOGIN FUNCTIONALITY ******

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email']; // Use null coalescing operator to handle undefined index
    $password = $_POST['password']; // Use null coalescing operator to handle undefined index
    
    $executorService = new Executor();
    $loginResult = $executorService->loginASPatient($email, $password);
    $doctorResult = $executorService->loginASDoctor($email, $password);

    

    

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
    header("location: ../view/patientDashboard.html");
    exit();
} 

if ($doctorResult) {
    # code...
    session_regenerate_id(true);

    $Initials = $initialsData['initials'] ?? strtoupper(
            $doctorResult['first_name'][0] . $loginResult['last_name'][0]
        );

    $_SESSION['LOGGED_IN_USER'] = true;
    $_SESSION['USER_ROLE'] = $doctorResult['user_role'];
    $_SESSION['USER_ID'] = $doctorResult['user_id'];
    $_SESSION['USER_EMAIL'] = $doctorResult["email"];
    $_SESSION['USER_NAME'] = $doctorResult['first_name'];
    $_SESSION['USER_SURNAME'] = $doctorResult['last_name'];
    $_SESSION["USER_INITIALS"] = $Initials;
    
    header("location: ../view/doctorDashboard.html");
    exit();
}
    // Missing email or password, redirect back to the login page with an error message 
    header("Location: login.php?error=missing");
    exit();

    
}
else {
        // Missing email or password, redirect back to the login page with an error message 
    header("Location: login.php?error=missing");
    exit();
    }

// ****** END OF LOGIN FUNCTIONALITY ******


?>