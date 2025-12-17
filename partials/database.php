<?php
$host="localhost";
$user="root";
$password="";
$database="e-commerce";
$conn=mysqli_connect($host,$user,$password,$database,3307);
if(!$conn){
   
    die("Error".mysqli_connect_error());
}
?>
