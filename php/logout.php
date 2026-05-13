<?php
require_once "DatabaseManager.php";
session_start();
session_destroy();
echo "
<p> User has been logout of account!</p>
";
header("location: ../index.html");
?>