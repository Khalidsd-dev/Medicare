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

    public function loginASDoctor($email, $password) {
        return $this->databaseManager->loginToDatabaseAsDoctorWithCredentials($email, $password);
    }

    public function loginASAdmin($email, $password) {
        return $this->databaseManager->loginToDatabaseAsAdminWithCredentials($email, $password);
    }

    public function saveUserCredentials($email, $password) {
        return $this->databaseManager->SaveUserCreadentials($email, $password);
    }

    public function storeUserData($userData) {
        return $this->databaseManager->StoreUserData($userData);
    }


    public function fetchAllAppointments() {
        return $this->databaseManager->fetchAllAppointments();
    }

   
    
    
}



?>