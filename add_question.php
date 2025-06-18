<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$quizId = $_GET['quiz_id'] ?? 0;

// Check quiz exists
$quiz = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$quiz->execute([$quizId]);
$quiz = $quiz->fetch();
if (!$quiz) {
    die("Quiz not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question_text'];
    $a = $_POST['option_a'];
    $b = $_POST['option_b'];
    $c = $_POST['option_c'];
    $d = $_POST['option_d'];
    $correct = $_POST['correct_option'];

    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quizId, $question, $a, $b, $c, $d, $correct]);

    echo "<div class='message'>Question added. Add another or <a href='quiz_list.php'>Go to Quiz List</a></div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f6fb;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 700px;
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

        form label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            margin-top: 25px;
            padding: 12px 20px;
            width: 100%;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            margin: 20px auto;
            width: fit-content;
            border-radius: 6px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Questions to: <?= htmlspecialchars($quiz['quiz_title']) ?></h2>
        <form method="POST">
            <label>Question:</label>
            <textarea name="question_text" required></textarea>

            <label>Option A:</label>
            <input type="text" name="option_a" required>

            <label>Option B:</label>
            <input type="text" name="option_b" required>

            <label>Option C:</label>
            <input type="text" name="option_c" required>

            <label>Option D:</label>
            <input type="text" name="option_d" required>

            <label>Correct Option:</label>
            <select name="correct_option" required>
                <option value="">Select correct answer</option>
                <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
                <option value="d">D</option>
            </select>

            <button type="submit">Add Question</button>
        </form>
    </div>
</body>
</html>
