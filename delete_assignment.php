<?php
session_start();
require 'db.php';

// Check if the teacher is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $assignmentId = $_GET['id'];

    // Fetch assignment details to get the file path
    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = ?");
    $stmt->execute([$assignmentId]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($assignment) {
        // Delete the assignment file if it exists
        if (file_exists($assignment['file_path'])) {
            unlink($assignment['file_path']);
        }

        // Delete the assignment from the database
        $stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ?");
        $stmt->execute([$assignmentId]);

        header("Location: view_assignments.php");
        exit;
    } else {
        echo "Assignment not found.";
    }
}
?>
