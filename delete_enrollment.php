<?php
// delete_enrollment.php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Get the enrollment ID from the query string
$enrollmentId = isset($_GET['id']) ? $_GET['id'] : 0;

if ($enrollmentId) {
    try {
        // Delete the enrollment from the database
        $sql = "DELETE FROM enrollments WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $enrollmentId]);

        // Redirect to the enrollments page after successful deletion
        header('Location: view_enrollments.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid enrollment ID.";
}
?>
