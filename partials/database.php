<?php
$conn = new mysqli(
    "host.docker.internal", // MySQL running on host (XAMPP)
    "techuser",             // DB user
    "techpass",             // DB password
    "techshop",             // Database name
    3306                    // MySQL port
);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
?>
