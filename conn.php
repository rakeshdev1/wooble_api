<?php
// Create 4 variables to store these information
$server="localhost";
$username="root";
$password="";
$database = "wooble";
// Create connection
$conn = new mysqli($server, $username, $password, $database);
// Check connection
//echo "Connected";
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  echo "Problem";
}
?>