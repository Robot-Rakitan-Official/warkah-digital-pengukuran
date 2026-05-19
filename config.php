<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "s0t0kudus"; 
$db   = "db_warkah";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>