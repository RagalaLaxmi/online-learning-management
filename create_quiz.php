<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacherId = $_SESSION['user_id'];

// Get all courses
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $course = $_POST['course_id'];

    $stmt = $pdo->prepare("INSERT INTO quizzes (course_id, quiz_title, description) VALUES (?, ?, ?)");
    $stmt->execute([$course, $title, $desc]);

    $quizId = $pdo->lastInsertId();
    header("Location: add_question.php?quiz_id=" . $quizId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Quiz</h2>
        <form method="POST">
            <label>Quiz Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description"></textarea>

            <label>Course:</label>
            <select name="course_id" required>
                <option value="">Select course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Create Quiz</button>
        </form>
    </div>
</body>
</html>
