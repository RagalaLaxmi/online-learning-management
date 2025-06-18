<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lms_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO announcements (title, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $message);
    $stmt->execute();
    $stmt->close();
    header("Location: send_announcements.php");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM announcements WHERE id = $id");
    header("Location: send_announcements.php");
    exit;
}

// Read
$results = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Announcements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        h1 { color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #aaa;
            text-align: left;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            margin-top: 10px;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Send Announcements</h1>

    <!-- Add Announcement Form -->
    <form method="post">
        <input type="hidden" name="add" value="1">
        <label>Title:</label>
        <input type="text" name="title" required>
        <br><br>
        <label>Message:</label>
        <textarea name="message" rows="4" required></textarea>
        <br>
        <input type="submit" value="Send Announcement">
    </form>

    <!-- Display Announcements -->
    <h2>Previous Announcements</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Posted On</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $results->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td class="actions">
                    <a href="edit_announcement.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
