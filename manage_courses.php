<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php'); // Redirect to login page if not logged in as teacher
    exit;
}

// Fetch all courses from the database
$stmt = $pdo->query("SELECT * FROM courses");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete a course
if (isset($_GET['delete'])) {
    $courseId = $_GET['delete'];
    // Fetch the course to delete its associated video file
    $stmt = $pdo->prepare("SELECT video_path FROM courses WHERE id = :id");
    $stmt->execute(['id' => $courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course) {
        unlink($course['video_path']); // Delete the video file from server
    }

    // Delete the course from the database
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
    $stmt->execute(['id' => $courseId]);

    header("Location: manage_courses.php"); // Redirect to manage courses page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Manage Courses</h1>

    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Description</th>
                <th>Video</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= htmlspecialchars($course['course_name']) ?></td>
                    <td><?= htmlspecialchars($course['course_description']) ?></td>
                    <td><a href="<?= htmlspecialchars($course['video_path']) ?>" target="_blank">Watch Video</a></td>
                    <td>
                        <a href="edit_course.php?id=<?= $course['id'] ?>">Edit</a> |
                        <a href="manage_courses.php?delete=<?= $course['id'] ?>" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="add_course.php" class="button">Add New Course</a>
</div>

</body>
</html>
