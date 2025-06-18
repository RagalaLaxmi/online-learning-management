<?php
session_start();
include('db.php'); // Include database connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];

    // Handle video upload
    $video_path = '';
    if (isset($_FILES['video']) && $_FILES['video']['error'] == UPLOAD_ERR_OK) {
        $video_tmp_name = $_FILES['video']['tmp_name'];
        $video_name = basename($_FILES['video']['name']);
        $video_dir = 'uploads/';

        // Create the uploads directory if it doesn't exist
        if (!is_dir($video_dir)) {
            mkdir($video_dir, 0755, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($video_tmp_name, $video_dir . $video_name)) {
            $video_path = $video_dir . $video_name; // Store the path for the database
        } else {
            $errorMessage = "Failed to upload video.";
        }
    }

    // Insert the new course into the database
    $query = "INSERT INTO courses (course_name, course_description, video_path) VALUES (:course_name, :course_description, :video_path)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['course_name' => $course_name, 'course_description' => $course_description, 'video_path' => $video_path]);

    // Redirect to the courses list page
    header('Location: view_courses.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="styles10.css">
</head>
<body>

    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="view_users.php">Manage Users</a></li>
            <li><a href="view_instructors.php">Manage Instructors</a></li>
            <li><a href="view_courses.php">Manage Courses</a></li>
            <li><a href="view_enrollments.php">Manage Enrollments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Add New Course</h1>

        <?php if (isset($errorMessage)): ?>
            <div style="color: red;"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form action="add_course.php" method="POST" enctype="multipart/form-data">
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" required><br><br>
            
            <label for="course_description">Course Description:</label>
            <textarea id="course_description" name="course_description" required></textarea><br><br>

            <label for="video">Upload Video:</label>
            <input type="file" id="video" name="video" accept="video/*" required><br><br>

            <button type="submit">Add Course</button>
        </form>
    </div>

</body>
</html>