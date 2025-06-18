<?php
session_start();
include('db.php'); // Include the database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Check if the user ID is provided in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch the user data based on the ID
    $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no user is found, redirect to the user list
    if (!$user) {
        header('Location: view_users.php');
        exit;
    }
}

// Handle form submission for updating user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Update the user data in the database
    $updateQuery = "UPDATE users SET username = :username, role = :role WHERE id = :id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute(['username' => $username, 'role' => $role, 'id' => $userId]);

    // Redirect back to the user list
    header('Location: view_users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?= $user['id'] ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>
            
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="teacher" <?= $user['role'] == 'teacher' ? 'selected' : '' ?>>Teacher</option>
            </select><br><br>

            <button type="submit">Update User</button>
        </form>
    </div>

</body>
</html>
