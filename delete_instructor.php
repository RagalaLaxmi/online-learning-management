<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $instructorId = $_GET['id'];

    // Delete the instructor from the database
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $instructorId]);

    // Redirect back to the instructor list page
    header('Location: view_instructors.php');
    exit;
} else {
    // If no ID is provided, redirect to the instructors list
    header('Location: view_instructors.php');
    exit;
}
?>
