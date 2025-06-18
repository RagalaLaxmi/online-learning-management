<?php
session_start();
include('db.php'); // Include database connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $courseId = $_GET['id'];

    // Delete the course from the database
    $query = "DELETE FROM courses WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $courseId]);

    // Redirect back to the course list page
    header('Location: view_courses.php');
    exit;
} else {
    // If no ID is provided, redirect to the courses list
    header('Location: view_courses.php');
    exit;
}