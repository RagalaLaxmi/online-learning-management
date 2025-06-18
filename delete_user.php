<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Check if the user ID is provided in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Delete the user from the database
    $deleteQuery = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute(['id' => $userId]);

    // Redirect back to the user list after deletion
    header('Location: view_users.php');
    exit;
} else {
    // If no user ID is provided, redirect to the user list
    header('Location: view_users.php');
    exit;
}
