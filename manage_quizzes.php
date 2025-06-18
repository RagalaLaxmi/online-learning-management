<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// Get all quizzes and course names (all quizzes, no filter by teacher)
$quizzes = $pdo->query("
    SELECT q.*, c.course_name 
    FROM quizzes q 
    JOIN courses c ON q.course_id = c.id
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Quizzes</title>
    <style>
        table { width: 80%; margin: auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Manage All Quizzes</h2>
    <div style="width: 80%; margin: auto;">
        <a href="create_quiz.php">+ Create New Quiz</a>
        <table>
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Course</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td><?= htmlspecialchars($quiz['quiz_title']) ?></td>
                        <td><?= htmlspecialchars($quiz['course_name']) ?></td>
                        <td><?= htmlspecialchars($quiz['description']) ?></td>
                        <td>
                            <a href="edit_quiz.php?id=<?= $quiz['id'] ?>">Edit</a> |
                            <a href="edit_questions.php?quiz_id=<?= $quiz['id'] ?>">Questions</a> |
                            <a href="delete_quiz.php?id=<?= $quiz['id'] ?>" onclick="return confirm('Delete this quiz?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($quizzes)): ?>
                    <tr><td colspan="4" style="text-align:center;">No quizzes found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
