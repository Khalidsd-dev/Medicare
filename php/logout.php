<?php
require_once "DatabaseManager.php";
require_once "Executor.php";


session_start();

// Clear all session variables
$_SESSION = [];

// Destroy session
session_destroy();

header("location: ../index.html");
exit();
?>