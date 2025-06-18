<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Not logged in.");
}

$userId = $_SESSION['user_id'];
$assignmentId = $_POST['assignment_id'] ?? null;

if (!$assignmentId || !isset($_FILES['submission_file'])) {
    die("Invalid request.");
}

$pdo = new PDO("mysql:host=localhost;dbname=lms_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if already submitted
$stmt = $pdo->prepare("SELECT id FROM submissions WHERE user_id = ? AND assignment_id = ?");
$stmt->execute([$userId, $assignmentId]);
if ($stmt->rowCount() > 0) {
    die("You already submitted this assignment.");
}

// Handle file upload
$uploadDir = "submissions/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$file = $_FILES['submission_file'];
$filename = time() . "_" . basename($file['name']);
$targetPath = $uploadDir . preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $filename);

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $stmt = $pdo->prepare("INSERT INTO submissions (user_id, assignment_id, file_path) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $assignmentId, $targetPath]);

    header("Location: view_assignments_user.php");
    exit;
} else {
    die("Failed to upload.");
}
?>
