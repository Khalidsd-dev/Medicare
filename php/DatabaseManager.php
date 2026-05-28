<?php
require_once "database.php";
require_once "db_connect.php";
include_once "Executor.php";
require_once "appointmentManager.php";



/**
 * This file defines the DatabaseManager class, 
 * which is responsible for managing database connections and queries related to user authentication for patients, doctors, and admins.
 * The class includes methods for logging in as a patient, doctor, or admin using their respective credentials, 
 * as well as methods for saving user credentials and storing user data. The class also includes placeholder methods for loading user data, 
 * request history, and response history, which can be implemented in the future as needed.
 * 
 */




class DatabaseManager {
private $request;
private $response;

private $database;
private $appointmentManager;

public function __construct() {
    // The constructor is currently empty, but it can be used to initialize any necessary properties or dependencies in the future.
}


/*
Response and request are not used in this class, but they are included in the constructor for potential future use.
The main purpose of this class is to manage database connections and queries related to user authentication for patients, 
doctors, and admins. Each login method connects to the database and retrieves user information based on the provided credentials.
*/





public function loginToDatabaseAsPatientWithCredentials($email, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Had an bug which I found the culprit with this debug eppression
        // var_dump([
        //     "DB_ROLE" => $user['user_role'] ?? null,
        //     "INPUT_ROLE_EXPECTED" => "PATIENT",
        //     "DB_PASSWORD" => $user['password'] ?? null,
        //     "INPUT_PASSWORD" => $password
        // ]);
        // exit;

        if ($user && $user['user_role'] === 'PATIENT' && $user['password'] === $password) {
            # code...
            return $user;
        }
        return false;

    } catch (Exception $e) {
        die($e->getMessage());
    }
}




public function loginToDatabaseAsDoctorWithCredentials($email, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doctor && $doctor['user_role'] !== "DOCTOR" && $doctor['password'] !== trim($password)) {
            return false;
        }
        if ($doctor['user_role'] === 'PATIENT' || $doctor['user_role'] === 'ADMIN') {
            return false;
        }

        return $doctor;

    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}


public function loginToDatabaseAsAdminWithCredentials($email, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($admin && $admin['user_role'] === 'ADMIN' && $admin['password'] === $password) {
            return $admin;
        }

        return false;
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

public function SaveUserCreadentials($email, $password) {    
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("INSERT INTO users (user_email, user_password) VALUES (?,?)");
        $stmt->execute([$email, $password]);
        return $stmt->fetch();
    } catch (\Throwable $th) {
        //throw $th;
        die("Database connection failed: " . $th->getMessage());
    }
}

public function StoreUserData($userData) {
    // This method is currently a placeholder and does not have an implementation.
    // It can be used in the future to store user data in the database or other storage systems.


}




public function loadUserData() {
    // This method is currently a placeholder and does not have an implementation.
    // It can be used in the future to load user data from the database or other sources.
}

public function loadRequestHistory() {
    // This method is currently a placeholder and does not have an implementation.
    // It can be used in the future to load request history from the database or other sources.
}

public function loadResponseHistory() {
    // This method is currently a placeholder and does not have an implementation.
    // It can be used in the future to load response history from the database or other sources.
}



public function scheduleAppointment() {
    return $this->appointmentManager->bookAppointment(); // Check functionality of this function
}



public function viewAppointments($patientId) {
    try {
        $pdo = require_once __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE patient_id = ?");
        $stmt->execute([$patientId]);

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll();
        } else {
            return false; // No appointments found
        }
    } catch (\Throwable $th) {
        //throw $th;
        die("Database connection failed: " . $th->getMessage());
    }
}

// This method fetches all appointments from the database, regardless of the patient ID. 
// It can be used for administrative purposes or for doctors to view all appointments.
public function fetchAllAppointments() {
    try {
        $pdo = require_once __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM appointments");
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll();
        } else {
            return false; // No appointments found
        }
    } catch (\Throwable $th) {
        //throw $th;
        die("Database connection failed: " . $th->getMessage());
    }
}



}