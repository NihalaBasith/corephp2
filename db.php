<?php
// db.php
$servername = "localhost";  // Typically 'localhost' for local development
$username = "root";         // XAMPP/WAMP default MySQL username
$password = "";             // XAMPP/WAMP default MySQL password is empty
$dbname = "file_management_system";  // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
