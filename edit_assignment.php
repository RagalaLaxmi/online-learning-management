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

    // Fetch assignment details to edit
    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = ?");
    $stmt->execute([$assignmentId]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        die("Assignment not found.");
    }

    // Fetch all courses for the teacher
    $teacherId = $_SESSION['teacher_id'];
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $course_id = $_POST['course_id'];

        // Handle file upload
        $filePath = $assignment['file_path'];  // Keep the current file if not uploading a new one
        if (!empty($_FILES['attachment']['name'])) {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = basename($_FILES["attachment"]["name"]);
            $filePath = $uploadDir . time() . "_" . $fileName;

            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $filePath)) {
                // Delete old file if it exists
                if (file_exists($assignment['file_path'])) {
                    unlink($assignment['file_path']);
                }
            } else {
                $error = "Failed to upload file.";
            }
        }

        // Update the assignment
        $stmt = $pdo->prepare("UPDATE assignments SET title = ?, description = ?, due_date = ?, course_id = ?, file_path = ? WHERE id = ?");
        $stmt->execute([$title, $description, $due_date, $course_id, $filePath, $assignmentId]);

        header("Location: view_assignments.php");
        exit;
    }
} else {
    die("Assignment ID not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="styles10.css">
</head>
<body>

<div class="sidebar">
    <h2>Teacher Dashboard</h2>
    <ul>
        <li><a href="view_course1.php">Manage Courses</a></li>
        <li><a href="view_assignments.php">Manage Assignments</a></li>
        <li><a href="view_quizzes.php">Manage Quizzes</a></li>
        <li><a href="view_student_Progress.php">View Student Progress</a></li>
        <li><a href="view_Grade_Assignments.php">Grade Assignments</a></li>
        <li><a href="send_Announcements.php">Send Announcements</a></li>
        <li><a href="student_interaction.php">Student Interaction</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Edit Assignment</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($assignment['title']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description">Description:</label>
            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($assignment['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" value="<?= htmlspecialchars($assignment['due_date']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="course_id">Course:</label>
            <select name="course_id" class="form-select" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= $course['id'] == $assignment['course_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="attachment">Change File (optional):</label>
            <input type="file" name="attachment" class="form-control">
            <?php if ($assignment['file_path']): ?>
                <p>Current File: <a href="<?= $assignment['file_path'] ?>" target="_blank">View File</a></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update Assignment</button>
    </form>
</div>

</body>
</html>
