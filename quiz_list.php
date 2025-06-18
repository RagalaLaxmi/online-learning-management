<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all quizzes with course name
$quizzes = $pdo->query("SELECT q.*, c.course_name FROM quizzes q JOIN courses c ON q.course_id = c.id")->fetchAll();
?>

<h2>Available Quizzes</h2>
<ul>
    <?php foreach ($quizzes as $quiz): ?>
        <li>
            <strong><?= htmlspecialchars($quiz['quiz_title']) ?></strong>
            (Course: <?= htmlspecialchars($quiz['course_name']) ?>)
            - <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>">Take Quiz</a>
        </li>
    <?php endforeach; ?>
</ul>
