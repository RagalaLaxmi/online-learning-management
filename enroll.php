<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $courseId = $_POST['course_id'];

    $host = 'localhost';
    $dbname = 'lms_db';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if already enrolled
        $check = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
        $check->execute([$userId, $courseId]);

        if ($check->rowCount() === 0) {
            $enroll = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, enrollment_date, completed) VALUES (?, ?, NOW(), 0)");
            $enroll->execute([$userId, $courseId]);
        }

        header('Location: user_dashboard.php');
        exit;

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    echo "Invalid request.";
}
?>
