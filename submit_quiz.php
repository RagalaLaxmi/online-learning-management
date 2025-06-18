<?php
session_start();
require 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

$userId = $_SESSION['user_id'];

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$quizId = $_POST['quiz_id'] ?? 0;
$answers = $_POST['answers'] ?? [];

// Check if quiz exists
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quizId]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Quiz not found.");
}

// Check if user already submitted the quiz
$stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE user_id = ? AND quiz_id = ?");
$stmt->execute([$userId, $quizId]);
if ($stmt->fetchColumn() > 0) {
    die("You have already taken this quiz. Multiple attempts are not allowed.");
}

// Fetch all questions for this quiz
$stmt = $pdo->prepare("SELECT id, correct_option FROM questions WHERE quiz_id = ?");
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$score = 0;
$total = count($questions);

// Evaluate answers
foreach ($questions as $question) {
    $qid = $question['id'];
    $correct = strtolower(trim($question['correct_option']));
    $selected = isset($answers[$qid]) ? strtolower(trim($answers[$qid])) : null;

    if ($selected === $correct) {
        $score++;
    }
}

$percentage = $total > 0 ? ($score / $total) * 100 : 0;

// Save result
$stmt = $pdo->prepare("
    INSERT INTO results (user_id, quiz_id, score, total, percentage)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$userId, $quizId, $score, $total, $percentage]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Result</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 50px; }
        .result-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            display: inline-block;
        }
        .result-box h2 { color: #4CAF50; }
        .result-box p { font-size: 18px; }
        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="result-box">
    <h2>Quiz Submitted: <?= htmlspecialchars($quiz['quiz_title']) ?></h2>
    <p><strong>Your Score:</strong> <?= $score ?> / <?= $total ?></p>
    <p><strong>Percentage:</strong> <?= round($percentage, 2) ?>%</p>
    <a href="quiz_list.php">Back to Quizzes</a>
</div>

</body>
</html>
