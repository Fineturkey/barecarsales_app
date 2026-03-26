<?php
$host = "localhost";
$user = "root";
$password = "i8yourmom2";
$database = "carsales_app";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>