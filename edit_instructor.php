<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $instructorId = $_GET['id'];

    // Fetch the instructor data based on ID
    $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $instructorId]);
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$instructor) {
        header('Location: view_instructors.php');
        exit;
    }
}

// Handle form submission to update instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Update the instructor data in the database
    $updateQuery = "UPDATE users SET username = :username, role = :role WHERE id = :id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute(['username' => $username, 'role' => $role, 'id' => $instructorId]);

    // Redirect back to the instructor list page
    header('Location: view_instructors.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Instructor</title>
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
        <h1>Edit Instructor</h1>

        <form action="edit_instructor.php?id=<?= $instructor['id'] ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($instructor['username']) ?>" required><br><br>
            
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="teacher" <?= $instructor['role'] == 'teacher' ? 'selected' : '' ?>>Teacher</option>
            </select><br><br>

            <button type="submit">Update Instructor</button>
        </form>
    </div>

</body>
</html>
