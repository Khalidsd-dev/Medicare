<?php

class appointment {

private $user;
private $date;
private $time;

 public function __construct($user, $date, $time) {
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;
    }

    public function getUser() {
        return $this->user;
    }
    public function getDate() {
        return $this->date;
    }

    public function getTime() {
        return $this->time;
    }

    function __toString() {
        return "Appointment for " . $this->user . " on " . $this->date 
        . " at " . $this->time;
    }

    public function save() {
        try {
            if (!file_exists("appointments.txt")) {
                throw new Exception("File not found.");
            }
            else {
                $file = fopen("appointments.txt", "a");
                fwrite($file, $this->user . "," . $this->date . "," . $this->time . "\n");
                fclose($file);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return;
        }
        
    }
}