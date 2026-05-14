<?php

require_once 'db_connect.php';

class database {
private $request;
private $response;


public function __construct($request) {
    $this->request = $request;
}


public function getResponse() {
    return $this->response;
}

public function getRequest() {
    return $this->request;
}

public function ResponseWithJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
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