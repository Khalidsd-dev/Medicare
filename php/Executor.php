<?php
include_once 'DatabaseManager.php';


class Executor {
    public $databaseManager;

    public function __construct() {
        $this->databaseManager = new DatabaseManager();
    }

    public function fetchAllAppointments() {
        return $this->databaseManager->fetchAllAppointments();
    }

   
    public function login($email, $password) {
        return $this->databaseManager->login($email, $password);
    }
}



?>