<?php
$host = "db";
$user = "root";
$password = "root";
$database = "techshop";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}
?>
