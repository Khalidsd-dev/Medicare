<?php
session_start(); // Start the session to manage user authentication and store session variables

header('Content-Type: application/json'); // Set the content type to JSON for API responses

if (isset($_SESSION['LOGGED_IN_USER']) && $_SESSION['LOGGED_IN_USER'] === true) {
    // User is logged in, return user data as JSON
    
    echo json_encode([
        'user_id' => $_SESSION['USER_ID'],
        'user_name' => $_SESSION['USER_NAME'],
        'user_surname' => $_SESSION['USER_SURNAME'],
        'user_email' => $_SESSION['USER_EMAIL']
    ]);
} else {
    http_response_code(401); // Set HTTP status code to 401 Unauthorized

    echo json_encode([
        'error' => 'UUser not logged in'
    ]);
}


class API {
    // This class can be expanded to include more API endpoints and functionality as needed

    public function fetchAllAppointments() {
        // This method can be implemented to fetch all appointments for the logged-in user
        // It would typically involve querying the database and returning the results as JSON

        // Example implementation (this is just a placeholder and should be replaced with actual database logic):

        $appointments = [
        ];

        echo json_encode($appointments);
    }
}
?>



