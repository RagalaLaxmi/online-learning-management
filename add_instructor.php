<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle form submission to add a new instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'teacher'; // Always set role as 'teacher' for instructors

    // Password validation
    if (strlen($password) < 6) {
        $errorMessage = "Password must be at least 6 characters long.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if username already exists
        $checkQuery = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->execute(['username' => $username]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $errorMessage = "Username already exists. Please choose another one.";
        } else {
            // Insert the new instructor into the database
            $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'role' => $role]);

            // Redirect to the instructors list page
            header('Location: view_instructors.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Instructor</title>
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
        <h1>Add New Instructor</h1>

        <?php if (isset($errorMessage)): ?>
            <div style="color: red;"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form action="add_instructor.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Add Instructor</button>
        </form>
    </div>

</body>
</html>
