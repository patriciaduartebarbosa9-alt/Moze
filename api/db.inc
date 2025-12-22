<?php
// Database connection details
$host = 'localhost';
$username = 'root';
$password = ''; // Update with your MySQL password
$database = 'mustache'; // Update with your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
