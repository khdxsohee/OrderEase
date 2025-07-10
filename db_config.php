<?php
// db_config.php
$servername = "localhost";
$username = "root"; // Common default username for local setups without password
$password = ""; // Leave this empty if your MySQL user has no password
$dbname = "tracking_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Set character set to utf8mb4 for proper display of characters
$conn->set_charset("utf8mb4");
?>