<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);



$host = "localhost";
$user = "root";
$password = "Rehamat130@"; // use your actual password
$dbname = "clothing-store";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
