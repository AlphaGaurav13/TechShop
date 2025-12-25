<?php
$conn = mysqli_connect(
  'mysql',        // service name
  'root',         // user
  'root',         // password
  'techshop_db'   // database
);

if (!$conn) {
  die("DB Error: " . mysqli_connect_error());
}

?>
