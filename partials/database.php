<?php
$conn = new mysqli(
    "host.docker.internal", // 🔥 yahin change
    "root",
    "123",
    "techshop",
    3307
);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

?>