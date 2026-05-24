<?php
require_once "DatabaseManager.php";
require_once "Executor.php";


session_start();

session_unset();
session_destroy();

header("location: ../index.html");
exit();


?>