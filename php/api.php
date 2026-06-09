<?php
session_start(); // Start the session to manage user authentication and store session variables

header('Content-Type: application/json'); // Set the content type to JSON for API responses

if (isset($_SESSION['LOGGED_IN_USER']) && $_SESSION['LOGGED_IN_USER'] === true) {
    // User is logged in, return user data as JSON
    
 
    echo json_encode([
        'user_id' => $_SESSION['USER_ID'],
        'user_name' => $_SESSION['USER_NAME'],
        'user_surname' => $_SESSION['USER_SURNAME'],
        'user_initials' => $_SESSION['USER_INITIALS'],
        'user_role' => $_SESSION['USER_ROLE']
    ]);
} else {
    http_response_code(401); // Set HTTP status code to 401 Unauthorized

    echo json_encode([
        'error' => 'User not logged in'
    ]);
}





?>



