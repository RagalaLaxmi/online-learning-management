<?php
session_start();
require 'db.php';

// Ensure the teacher is logged in
if (!isset($_SESSION['teacher_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacherId = $_SESSION['teacher_id'];

// Fetch all available courses (not just those assigned to this teacher)
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(); // Get all courses

// Handle form submission for creating a new quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $course = $_POST['course_id'];

    // Insert the new quiz into the database
    $stmt = $pdo->prepare("INSERT INTO quizzes (course_id, quiz_title, description) VALUES (?, ?, ?)");
    $stmt->execute([$course, $title, $desc]);

    // Get the last inserted quiz ID to redirect to the add_question.php page
    $quizId = $pdo->lastInsertId();

    // Redirect to the add_question.php page for the created quiz
    header("Location: add_question.php?quiz_id=" . $quizId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 50px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, select {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .courses-list {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Quiz</h2>

    <form method="POST">
        <label for="title">Quiz Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>

        <label for="course_id">Course:</label>
        <select name="course_id" id="course_id" required>
            <option value="">Select a course</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Create Quiz</button>
    </form>
</div>

</body>
</html>
