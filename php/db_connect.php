<?php

require_once __DIR__ . '/../vendor/autoload.php';

// load .env from env/config.env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../env', 'config.env');
$dotenv->load();

// PDO connection parameters
$username = $_ENV['DB_USER'];
$dbpassword = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];
$host = $_ENV['DB_HOST'];
$charset = $_ENV["DB_CHARSET"];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $dbpassword, 
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    // set the PDO error mode to exception  

}
catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

return $pdo;
?>