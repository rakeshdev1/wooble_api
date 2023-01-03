<?php
// Create 4 variables to store these information
$server="217.21.80.10";
$username="u502039025_h85";
$password="Tp9M!o7Vc?";
$database = "u502039025_h85";
// Create connection
$conn = new mysqli($server, $username, $password, $database);
// Check connection
//echo "Connected";
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  echo "Problem";
}
