<?php
// Logout script for AmEuro System
// Destroy session and redirect to login
session_start();
$_SESSION = array();
session_destroy();
header("Location: login.php");
exit;
?>