<?php
$host = "db";          
$user = "techuser";
$password = "techpass";
$database = "techshop";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}
?>
