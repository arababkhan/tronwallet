<?php
// Database Configuration
$dbName = 'wallet';
$dbUser = 'root';
$dbPassword = '';
$main_address = 'TXMA8hp9GoMNbsYUfk6E24WkkNEZ5RToKn';

// Establish a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>