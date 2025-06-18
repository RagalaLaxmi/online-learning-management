<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];

    $stmt = $pdo->prepare("UPDATE assignment_submissions SET grade = ? WHERE id = ?");
    $stmt->execute([$grade, $submission_id]);

    header("Location: view_Grade_Assignments.php");
    exit;
}
