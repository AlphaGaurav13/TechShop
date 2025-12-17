<?php
$host="localhost";
$user="root";
$password="";
$database="e-commerce";
$conn=mysqli_connect($host,$user,$password,$database);
if(!$conn){
   
    die("Error".mysqli_connect_error());
}
?>
