<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all instructors (role = 'teacher')
$query = "SELECT * FROM users WHERE role = 'teacher'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructors</title>
    <link rel="stylesheet" href="styles11.css">
</head>
<body>

    <!-- Sidebar -->
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

    <!-- Main content -->
    <div class="main-content">
        <h1>Manage Instructors</h1>

        <!-- Add Instructor Button -->
        <a href="add_instructor.php">
            <button class="add-instructor-button">Add New Instructor</button>
        </a><br><br>

        <!-- Instructor List Table -->
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instructors as $instructor): ?>
                    <tr>
                        <td><?= $instructor['id'] ?></td>
                        <td><?= htmlspecialchars($instructor['username']) ?></td>
                        <td><?= htmlspecialchars($instructor['role']) ?></td>
                        <td>
                            <a href="edit_instructor.php?id=<?= $instructor['id'] ?>">Edit</a> |
                            <a href="delete_instructor.php?id=<?= $instructor['id'] ?>" onclick="return confirm('Are you sure you want to delete this instructor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
