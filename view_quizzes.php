<?php
session_start();
require 'db.php';

// Check user role (only teachers can see this page)
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// Fetch all quizzes with course names
$stmt = $pdo->prepare("
    SELECT q.*, c.course_name
    FROM quizzes q
    JOIN courses c ON q.course_id = c.id
");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Quizzes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f5fa;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 40px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .create-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 16px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .create-btn:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .actions a {
            text-decoration: none;
            color: #007BFF;
            margin-right: 10px;
            padding: 5px 8px;
            border-radius: 4px;
            background-color: #e9f2ff;
            transition: background-color 0.2s ease;
        }

        .actions a:hover {
            background-color: #d0e5ff;
        }

        .actions a:last-child {
            color: #dc3545;
            background-color: #ffe9e9;
        }

        .actions a:last-child:hover {
            background-color: #ffcccc;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #555;
        }
    </style>
</head>
<body>
    <h2>Available Quizzes</h2>
    <div class="container">
        <a class="create-btn" href="create_quiz.php">+ Create New Quiz</a>
        <table>
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Course Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($quizzes) > 0): ?>
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz['quiz_title']) ?></td>
                            <td><?= htmlspecialchars($quiz['course_name']) ?></td>
                            <td class="actions">
                                <a href="edit_quiz.php?id=<?= $quiz['id'] ?>">Edit</a>
                                <a href="edit_questions.php?quiz_id=<?= $quiz['id'] ?>">Questions</a>
                                <a href="delete_quiz.php?id=<?= $quiz['id'] ?>" onclick="return confirm('Delete this quiz?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="no-data">No quizzes found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
