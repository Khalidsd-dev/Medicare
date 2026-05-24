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



public function ConnectToDatabase() {
       return $this->database->loginToDatabase();
}

public function loginToDatabaseAsPatientWithCredentials($email, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM user WHERE LOWER(user_email) = LOWER(?) AND user_password = ?");
        $stmt->bindparam(1, $email);
        $stmt->bindparam(2, $password);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
           return $stmt->fetch();
        }
        else {
            return false; // No matching user found
        }
        
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

public function loginToDatabaseAsDoctorWithCredentials($docID, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE docID = ? AND password = ?");
        $stmt->execute([$docID, $password]);
        return $stmt->fetch();
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}


public function loginToDatabaseAsAdminWithCredentials($adminID, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE adminId = ? AND password = ?");
        $stmt->execute([$adminID, $password]);
        return $stmt->fetch();
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

public function SaveUserCreadentials($email, $password) {    
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $stmt = $pdo->prepare("INSERT INTO user (user_email, user_password) VALUES (?,?)");
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
    }