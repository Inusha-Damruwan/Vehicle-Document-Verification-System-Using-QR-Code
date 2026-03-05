<?php
$servername = "localhost";
$username = "root"; // Default username for localhost
$password = "";     // Default password for XAMPP/MAMP/WAMP
$dbname = "vehicle_documentsdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>