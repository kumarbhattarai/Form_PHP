<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website";

// Connection for DB creation
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);
$conn->select_db($dbname);

// Create table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS form_data (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    utm_source VARCHAR(100),
    utm_medium VARCHAR(100),
    city VARCHAR(100),
    country VARCHAR(100),
    ip VARCHAR(50),
    file_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_sql);
?>
