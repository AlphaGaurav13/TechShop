<?php
$host = getenv("metro.proxy.rlwy.net");
$port = getenv("48007");
$user = getenv("root");
$pass = getenv("olypCUsRsqIuzJDcKVANOdLpwJZxxbFP");
$db   = getenv("railway");

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
?>
