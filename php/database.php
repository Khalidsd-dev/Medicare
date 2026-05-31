<?php

require 'db_connect.php';

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
        $pdo = require __DIR__ . '/db_connect.php';
        if (!($pdo instanceof PDO)) {
            throw new \RuntimeException('Database connection did not return a PDO instance');
        }
        return $pdo;
    }
    catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

}