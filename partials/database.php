<?php
$conn = new mysqli(
    "db",        // service name from docker-compose
    "techuser",  // MYSQL_USER
    "techpass",  // MYSQL_PASSWORD
    "techshop",  // MYSQL_DATABASE
    3306
);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
