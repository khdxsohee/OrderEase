<?php
// db_config.php
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "tracking_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// --- Admin Login Configuration ---
define('ADMIN_USERNAME', 'admin'); // Aap apna username bhi badal sakte hain
define('ADMIN_PASSWORD_HASH', '$2y$10$cTIkJLZOWLDfcnI8fOpbyO4hjYYocKFVff.TB/a/6i2Pp8AoK9geu'); // <--- YAHAN APNA COPIED HASHED PASSWORD PASTE KAREIN
// Example: define('ADMIN_PASSWORD_HASH', '$2y$10$abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwx');
// --- End Admin Login Configuration ---

?>