<?php
// Simple DB helper for local XAMPP development.
// Adjust credentials if you changed MySQL user/password.
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'hodini';

// Connect to MySQL server
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($conn->connect_error) {
    header('Content-Type: text/plain');
    echo 'ERROR: MySQL connection failed: ' . $conn->connect_error;
    exit;
}

// Create database if it doesn't exist
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `" . $conn->real_escape_string($DB_NAME) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    header('Content-Type: text/plain');
    echo 'ERROR: Create database failed: ' . $conn->error;
    exit;
}

$conn->select_db($DB_NAME);

// Create members table if missing
$createMembers = "CREATE TABLE IF NOT EXISTS `members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `fullname` VARCHAR(255),
    `email_verified` TINYINT(1) DEFAULT 0,
    `verification_token` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($createMembers)) {
    header('Content-Type: text/plain');
    echo 'ERROR: Create table failed: ' . $conn->error;
    exit;
}

// Ensure all required columns exist - add missing columns
$columnsToCheck = [
    'fullname' => "ALTER TABLE members ADD COLUMN fullname VARCHAR(255)",
    'email_verified' => "ALTER TABLE members ADD COLUMN email_verified TINYINT(1) DEFAULT 0",
    'verification_token' => "ALTER TABLE members ADD COLUMN verification_token VARCHAR(255)",
    'email' => "ALTER TABLE members ADD COLUMN email VARCHAR(255) NOT NULL UNIQUE"
];

foreach ($columnsToCheck as $colName => $alterQuery) {
    $checkCol = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='members' AND COLUMN_NAME='$colName'");
    if (!$checkCol || $checkCol->num_rows == 0) {
        // Column doesn't exist, try to add it
        @$conn->query($alterQuery);
    }
}

?>
