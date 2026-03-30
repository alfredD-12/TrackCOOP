<?php
// Database configuration for TrackCOOP
// Ensure 'trackcoop_db' is the database name in phpMyAdmin
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "trackcoop_db"; 

// Create connection using Object-Oriented approach
$conn = new mysqli($host, $user, $pass, $dbname);

// Better error handling
if ($conn->connect_error) {
    // In production, do not show detailed error, but okay for development
    die("Connection failed: " . $conn->connect_error);
}

// Set charset for special characters (like 'ñ' in names)
$conn->set_charset("utf8mb4");

// Optional: Set timezone to match Philippines
date_default_timezone_set('Asia/Manila');

// You can use this in other files: require_once 'db_connect.php';
?>