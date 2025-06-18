<?php
$host = 'localhost'; // your database host
$dbname = 'lms_db';  // your database name
$username = 'root';   // your database username
$password = '';       // your database password

// Create a connection to the MySQL database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
