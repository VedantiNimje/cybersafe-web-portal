<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "cyber_security";
//create connection
$conn = new mysqli($host, $username, $password, $database);
//check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>