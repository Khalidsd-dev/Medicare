<?php

require_once 'db_connect.php';

class database {
private $request;
private $response;


public function __construct() {
    // The constructor is currently empty, but it can be used to initialize any necessary properties or dependencies in the future.
}


public function getResponse() {
    return $this->response;
}

public function getRequest() {
    return $this->request;
}



public function loginToDatabase() {
    try {
        $pdo = require_once __DIR__ . '/db_connect.php';
        return $pdo;
    }
    catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

}