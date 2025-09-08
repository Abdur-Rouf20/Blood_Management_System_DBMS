<?php
// Database Connection
$host = "localhost";
$user = "root";      // change if needed
$pass = "";          // add password if you set one
$db   = "blood_management_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
