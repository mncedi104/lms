<?php
// Database Configuration
$host = 'localhost';
$dbname = 'mm_digimind_db';
$username = 'mm_Admin';
$password = 'M[Cxu??ghghu';

// Attempt to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // You can add more configuration options if needed
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
