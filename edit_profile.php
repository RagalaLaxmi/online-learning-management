<?php
session_start();

// Check user session and role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User ID not found in session. Please <a href='login.php'>login again</a>.</p>";
    exit;
}

$userId = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$dbname = 'lms_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get current user data
$stmt = $pdo->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        // Handle Save Changes
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Default profile picture is the current one unless the user uploads a new one
        $profilePicture = $user['profile_picture'];

        // If a new profile picture is uploaded
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            // Define upload directory
            $uploadDir = 'uploads/';
            $fileName = $_FILES['profile_picture']['name'];
            $uploadFile = $uploadDir . basename($fileName);

            // Move the uploaded file to the desired directory
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                $profilePicture = $uploadFile; // Update profile picture path
            } else {
                echo "<p>Error uploading profile picture.</p>";
            }
        }

        // Update the user profile in the database
        $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?");
        $updateStmt->execute([$username, $email, $profilePicture, $userId]);

        // Set a success message
        $_SESSION['message'] = "Profile updated successfully!";
        header('Location: user_dashboard.php');  // Redirect to user dashboard after saving changes
        exit;
    }

    if (isset($_POST['cancel'])) {
        // Handle Cancel - Redirect to user dashboard
        header('Location: user_dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles20.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: row;
            padding: 20px;
        }
        aside {
            width: 200px;
            background-color: #333;
            color: white;
            padding: 20px;
            height: 100vh;
        }
        aside h2 {
            text-align: center;
        }
        aside ul {
            list-style: none;
            padding: 0;
        }
        aside ul li {
            padding: 10px;
        }
        aside ul li a {
            color: white;
            text-decoration: none;
        }
        main {
            flex-grow: 1;
            padding: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        form button[type="submit"]:hover {
            background-color: #45a049;
        }
        form button[type="submit"]:last-child {
            background-color: #f44336;
        }
        form button[type="submit"]:last-child:hover {
            background-color: #da190b;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside>
            <h2>Dashboard</h2>
            <ul>
                <li><a href="user_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main>
            <h1>Edit Profile</h1>

            <!-- Profile Update Form -->
            <form method="POST" enctype="multipart/form-data">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture">

                <p>
                    <button type="submit" name="save">Save Changes</button>
                    <button type="submit" name="cancel">Cancel</button>
                </p>
            </form>
        </main>
    </div>
</body>
</html>
