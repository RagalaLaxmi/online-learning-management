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

$id = intval($_GET['id']);

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $stmt = $conn->prepare("UPDATE announcements SET title=?, message=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $message, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: send_announcements.php");
    exit;
}

// Get current values
$result = $conn->query("SELECT * FROM announcements WHERE id = $id");
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Announcement</title>
</head>
<body>
    <h1>Edit Announcement</h1>
    <form method="post">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" required><br><br>
        <label>Message:</label><br>
        <textarea name="message" rows="4" required><?= htmlspecialchars($row['message']) ?></textarea><br>
        <input type="submit" value="Update Announcement">
    </form>
</body>
</html>
