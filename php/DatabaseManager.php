<?php
require_once "database.php";
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

private function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}

private function verifyPassword(string $password, $passwordhash) {
    return password_verify($password, $passwordhash);
}

private function needsRehash(string $hash): bool {
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}



/*
Response and request are not used in this class, but they are included in the constructor for potential future use.
The main purpose of this class is to manage database connections and queries related to user authentication for patients, 
doctors, and admins. Each login method connects to the database and retrieves user information based on the provided credentials.
*/


function login($email, $password) {
    // This method is a placeholder for the login functionality. 
    // It can be implemented to handle user authentication based on the provided credentials.

    $pdo = require __DIR__ . '/db_connect.php';
    if (!($pdo instanceof PDO)) {
        throw new \RuntimeException('Database connection did not return a PDO instance');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

   
    if(!$user) {
        return false; // User not found
    }

    if ($this->verifyPassword($password, $user['password'])) {
        return $user; // Login successful
    } 
        return false; // Incorrect password
    }


public function viewAppointments($patientId) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        if (!($pdo instanceof PDO)) {
            throw new \RuntimeException('Database connection did not return a PDO instance');
        }
        $stmt = $pdo->prepare(
            "SELECT a.*, u.first_name AS doctor_first_name, u.last_name AS doctor_last_name, d.specialization AS doctor_specialty " .
            "FROM appointments a " .
            "JOIN users u ON a.doctor_id = u.user_id " .
            "JOIN doctors d ON a.doctor_id = d.doctor_id " .
            "WHERE a.patient_id = ? " .
            "ORDER BY a.appointment_date ASC, a.appointment_time ASC"
        );
        $stmt->execute([$patientId]);

        if ($stmt->rowCount() > 0) {
            $appointments = $stmt->fetchAll();
            return array_map(function ($appointment) {
                $appointment['doctor_name'] = trim($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']);
                $appointment['doctor_specialty'] = $appointment['doctor_specialty'] ?? '';
                unset($appointment['doctor_first_name'], $appointment['doctor_last_name']);
                return $appointment;
            }, $appointments);
        } else {
            return false; // No appointments found
        }
    } catch (\Throwable $th) {
        //throw $th;
        die("Database connection failed: " . $th->getMessage());
    }
}

public function viewDoctorAppointments($doctorId) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        if (!($pdo instanceof PDO)) {
            throw new \RuntimeException('Database connection did not return a PDO instance');
        }

        $stmt = $pdo->prepare(
            "SELECT a.*, u.first_name AS patient_first_name, u.last_name AS patient_last_name " .
            "FROM appointments a " .
            "JOIN users u ON a.patient_id = u.user_id " .
            "WHERE a.doctor_id = ? " .
            "ORDER BY a.appointment_date ASC, a.appointment_time ASC"
        );
        $stmt->execute([$doctorId]);

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    } catch (\Throwable $th) {
        die("Database connection failed: " . $th->getMessage());
    }
}

public function updateAppointmentStatus($appointmentId, $doctorId, $status) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        if (!($pdo instanceof PDO)) {
            throw new \RuntimeException('Database connection did not return a PDO instance');
        }

        $stmt = $pdo->prepare(
            "UPDATE appointments SET appointment_status = ?, updated_at = NOW() WHERE appointment_id = ? AND doctor_id = ?"
        );
        $stmt->execute([$status, $appointmentId, $doctorId]);

        return $stmt->rowCount() > 0;
    } catch (\Throwable $th) {
        die("Database connection failed: " . $th->getMessage());
    }
}

// This method fetches all appointments from the database, regardless of the patient ID. 
// It can be used for administrative purposes or for doctors to view all appointments.
public function fetchAllAppointments() {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        if (!($pdo instanceof PDO)) {
            throw new \RuntimeException('Database connection did not return a PDO instance');
        }
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

public function SaveUserCredentials($email, $password) {
    try {
        $pdo = require __DIR__ . '/db_connect.php';
        $hashedPassword = $this->hashPassword($password);
        $stmt = $pdo->prepare(
            "INSERT INTO users (email, password, first_name, last_name, gender, user_role, account_status) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$email, $hashedPassword, 'New', 'User', 'OTHER', 'PATIENT', 'ACTIVE']);
        return $pdo->lastInsertId();
    } catch (\Throwable $th) {
        die("Database connection failed: " . $th->getMessage());
    }
}



}