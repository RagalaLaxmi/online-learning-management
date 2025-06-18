<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$query = "SELECT * FROM courses";
$stmt = $pdo->query($query);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Courses</title>
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
        <h1>Courses List</h1>
        <table>
            <tr>
                <th>Course Name</th>
                <th>Description</th>
                <th>Video</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?= htmlspecialchars($course['course_name']) ?></td>
                <td><?= htmlspecialchars($course['course_description']) ?></td>
                <td>
                    <?php if ($course['video_path']): ?>
                        <a href="<?= htmlspecialchars($course['video_path']) ?>" target="_blank">Watch Video</a>
                    <?php else: ?>
                        No Video Uploaded
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_course.php?id=<?= $course['id'] ?>">Edit</a> |
                    <a href="delete_course.php?id=<?= $course['id'] ?>" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="add_course.php">Add New Course</a>
    </div>

</body>
</html>