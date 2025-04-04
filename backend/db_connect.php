<?php
$servername = "localhost"; // Change if using a remote database
$username = "s11600135_val_residences"; // Your database username
$password = "QW12erty"; // Your database password
$dbname = "s11600135_val_residences"; // Your database name

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding (optional but recommended)
$conn->set_charset("utf8");
?>