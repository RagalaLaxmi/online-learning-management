<?php
include 'db.php';

// Fetch all students
$query = "SELECT * FROM students";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();

// Fetch messages (if any)
$messagesQuery = "SELECT m.message, s.name AS student_name
                  FROM messages m
                  JOIN students s ON m.student_id = s.id";
$messagesStmt = $pdo->query($messagesQuery);
$messages = $messagesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $message = $_POST['message'];

    // Insert message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (student_id, message) VALUES (?, ?)");
    $stmt->execute([$student_id, $message]);

    header("Location: student_interaction.php");
}
?>

<div class="student-interactions">
    <h2>Student Interactions</h2>
    <div id="messages-list">
        <!-- Dynamic Messages List -->
    </div>
    <div id="feedback-list">
        <!-- Dynamic Feedback List -->
    </div>
</div>
