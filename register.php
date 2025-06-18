<?php
// register.php

require_once 'db.php'; // Include your database connection file
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $role = $_POST['role']; // Capture the role
    $email = trim($_POST['email']); // Capture email
    $profile_picture = ''; // Default empty, in case the user does not upload any picture

    // Handle the profile picture upload (if exists)
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/"; // Directory where profile pictures will be saved
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if the uploaded file is an actual image
        if (getimagesize($_FILES['profile_picture']['tmp_name']) === false) {
            $error = "File is not an image.";
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture = $target_file; // Save the path to the file
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }

    try {
        // Check if the username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Username already exists. Please choose another one.");
        }

        // If the role is admin, check if there are already 2 admins in the database
        if ($role == 'admin') {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            $stmt->execute();
            $adminCount = $stmt->fetchColumn();

            if ($adminCount >= 2) {
                throw new Exception("There can only be 2 admins. Registration for admins is closed.");
            }
        }

        // Prepare SQL to insert data including the new fields
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email, profile_picture) 
                               VALUES (:username, :password, :role, :email, :profile_picture)");

        // Bind the parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':profile_picture', $profile_picture);

        // Execute the query
        $stmt->execute();

        // Log the user in by setting session variables after registration (if desired)
        $_SESSION['username'] = $username;  // Set the username
        $_SESSION['role'] = $role;  // Set the role

        // Redirect to login page after successful signup
        header('Location: login.php?message=User registered successfully!');
        exit;

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    } catch (Exception $e) {
        $error = $e->getMessage(); // Display any custom exceptions (like too many admins)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" action="register.php" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br><br>

            <label for="role">Role:</label>
            <select name="role" required>
                <option value="user">User</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select><br><br>

            <label for="email">Email:</label>
            <input type="email" name="email" required><br><br>

            <label for="profile_picture">Profile Picture (Optional):</label>
            <input type="file" name="profile_picture" accept="image/*"><br><br>

            <button type="submit">Register</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</body>
</html>
