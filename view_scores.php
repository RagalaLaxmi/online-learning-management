<?php
session_start();
require 'db.php';

// Ensure only logged-in users (students) can view
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get the user's quiz attempts and results
$stmt = $pdo->prepare("
    SELECT q.quiz_title, c.course_name, qa.score, qa.submitted_at
    FROM quiz_attempts qa
    JOIN quizzes q ON q.id = qa.quiz_id
    JOIN courses c ON c.id = q.course_id
    WHERE qa.user_id = ?
    ORDER BY qa.submitted_at DESC
");
$stmt->execute([$userId]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Quiz Scores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>Your Quiz Scores</h2>

    <?php if ($results): ?>
        <table>
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Course</th>
                    <th>Score</th>
                    <th>Date Taken</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['quiz_title']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td><?= $row['score'] ?></td>
                        <td><?= $row['submitted_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No quiz attempts found.</p>
    <?php endif; ?>
</body>
</html>
