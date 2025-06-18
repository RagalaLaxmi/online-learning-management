<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $courseId = $_GET['id'];

    // Fetch the course data based on ID
    $query = "SELECT * FROM courses WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        header('Location: view_courses.php');
        exit;
    }
}

// Handle form submission to update course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $video_path = $course['video_path']; // Keep existing video path

    if (isset($_FILES['video']) && $_FILES['video']['error'] == UPLOAD_ERR_OK) {
        $video_tmp_name = $_FILES['video']['tmp_name'];
        $video_name = basename($_FILES['video']['name']);
        $video_dir = 'uploads/';

        if (move_uploaded_file($video_tmp_name, $video_dir . $video_name)) {
            $video_path = $video_dir . $video_name; // Update video path
        }
    }

    $updateQuery = "UPDATE courses SET course_name = :course_name, course_description = :course_description, video_path = :video_path WHERE id = :id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute(['course_name' => $course_name, 'course_description' => $course_description, 'video_path' => $video_path, 'id' => $courseId]);

    header('Location: view_courses.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="styles14.css">
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
        <h1>Edit Course</h1>

        <form action="edit_course.php?id=<?= $course['id'] ?>" method="POST" enctype="multipart/form-data">
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required><br><br>
            
            <label for="course_description">Course Description:</label>
            <textarea id="course_description" name="course_description" required><?= htmlspecialchars($course['course_description']) ?></textarea><br><br>

            <label for="video">Upload New Video (optional):</label>
            <input type="file" id="video" name="video" accept="video/*"><br><br>

            <button type="submit">Update Course</button>
        </form>
    </div>

</body>
</html>