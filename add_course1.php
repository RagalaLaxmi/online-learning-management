<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php'); // Redirect to login page if not logged in as teacher
    exit;
}

// Add a new course
if (isset($_POST['add_course'])) {
    $courseName = $_POST['course_name'];
    $courseDescription = $_POST['course_description'];

    // Handle video file upload
    if (isset($_FILES['course_video'])) {
        $videoPath = 'uploads/' . basename($_FILES['course_video']['name']);
        move_uploaded_file($_FILES['course_video']['tmp_name'], $videoPath);
    }

    // Insert course into the database
    $sql = "INSERT INTO courses (course_name, course_description, video_path) 
            VALUES (:course_name, :course_description, :video_path)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'course_name' => $courseName,
        'course_description' => $courseDescription,
        'video_path' => $videoPath
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
    <title>Add Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Add New Course</h1>

    <form action="add_course1.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <textarea name="course_description" placeholder="Course Description" required></textarea>
        <input type="file" name="course_video" accept="video/*" required>
        <button type="submit" name="add_course">Add Course</button>
    </form>
</div>

</body>
</html>
