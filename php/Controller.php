<?php


require_once "Executor.php";

session_start(); // Start the session to manage user authentication and store session variables


// ****** LOGIN FUNCTIONALITY ******

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email']; // Use null coalescing operator to handle undefined index
    $password = $_POST['password']; // Use null coalescing operator to handle undefined index
    
    $executorService = new Executor();
    $loginResult = $executorService->loginASPatient($email, $password);

// if loginresult fetch array contains email and password that match the input, then login successful, else login failed

    if (
        $loginResult && 
        $loginResult['user_email'] === $email && 
        $loginResult['user_password'] === $password
        ) {

        session_regenerate_id(); // Regenerate session ID to prevent session fixation attacks
        
        $_SESSION['LOGGED_IN_USER'] =  true; // Store the logged-in user's email in the session
        $_SESSION['USER_ID'] = $loginResult['user_id']; // Store the logged-in user's ID in the session
        $_SESSION['USER_EMAIL'] = $email; // Store the logged-in user's email in the session
        $_SESSION['USER_NAME'] = $loginResult['user_name']; // Store the logged-in user's name in the session
        $_SESSION['USER_SURNAME'] = $loginResult['user_surname'];
        $_SESSION['USER_GENDER'] = $loginResult['user_gender'];

        // Redirect to Dashboard
        header("location: ../view/patientDashboard.html");
        exit();

    } else {
        // Login failed, redirect back to the login page with an error message
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