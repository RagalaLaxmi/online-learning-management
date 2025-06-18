<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php'); // Redirect to login page if not logged in as teacher
    exit;
}

// Check if the course ID is provided for editing
if (!isset($_GET['id'])) {
    header('Location: manage_courses1.php'); // Redirect to manage courses page if no ID is given
    exit;
}

$courseId = $_GET['id'];

// Fetch the course data from the database
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
$stmt->execute(['id' => $courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the course exists
if (!$course) {
    header('Location: manage_courses1.php'); // Redirect if the course does not exist
    exit;
}

// Handle form submission to update the course
if (isset($_POST['update_course'])) {
    $courseName = $_POST['course_name'];
    $courseDescription = $_POST['course_description'];
    $videoPath = $course['video_path']; // Keep the existing video path if not updating

    // Handle video file upload if new video is uploaded
    if (isset($_FILES['course_video']) && $_FILES['course_video']['error'] == 0) {
        $videoPath = 'uploads/' . basename($_FILES['course_video']['name']);
        move_uploaded_file($_FILES['course_video']['tmp_name'], $videoPath);
    }

    // Update the course in the database
    $stmt = $pdo->prepare("UPDATE courses SET course_name = :course_name, course_description = :course_description, video_path = :video_path WHERE id = :id");
    $stmt->execute([
        'course_name' => $courseName,
        'course_description' => $courseDescription,
        'video_path' => $videoPath,
        'id' => $courseId
    ]);

    header("Location: manage_courses1.php"); // Redirect to manage courses page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Edit Course</h1>

    <form action="edit_course1.php?id=<?= $courseId ?>" method="POST" enctype="multipart/form-data">
        <input type="text" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required>
        <textarea name="course_description" required><?= htmlspecialchars($course['course_description']) ?></textarea>
        <input type="file" name="course_video" accept="video/*">
        <button type="submit" name="update_course">Update Course</button>
    </form>
</div>

</body>
</html>
