<?php
include_once 'DatabaseManager.php';


class Executor {
    public $databaseManager;

    public function __construct() {
        $this->databaseManager = new DatabaseManager();
    }

    public function loginASPatient($email, $password) {
        return $this->databaseManager->loginToDatabaseAsPatientWithCredentials($email, $password);
    }

    public function loginASDoctor($docID, $password) {
        return $this->databaseManager->loginToDatabaseAsDoctorWithCredentials($docID, $password);
    }

    public function loginASAdmin($adminID, $password) {
        return $this->databaseManager->loginToDatabaseAsAdminWithCredentials($adminID, $password);
    }

    public function saveUserCredentials($email, $password) {
        return $this->databaseManager->SaveUserCreadentials($email, $password);
    }

    public function storeUserData($userData) {
        return $this->databaseManager->StoreUserData($userData);
    }


    public function EstablishDatabaseConnection() {
        return $this->databaseManager->ConnectToDatabase();
    }

    public function fetchAllAppointments() {
        return $this->databaseManager->fetchAllAppointments();
    }

    
    
}



?>