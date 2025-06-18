<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$quizId = $_GET['quiz_id'] ?? 0;

// Check if already taken
$stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE user_id = ? AND quiz_id = ?");
$stmt->execute([$userId, $quizId]);
if ($stmt->fetchColumn() > 0) {
    die("You have already taken this quiz. Multiple attempts are not allowed.");
}

// Fetch quiz and questions
$quiz = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$quiz->execute([$quizId]);
$quiz = $quiz->fetch();

if (!$quiz) {
    die("Quiz not found.");
}

$questions = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$questions->execute([$quizId]);
$questions = $questions->fetchAll();
?>

<h2><?= htmlspecialchars($quiz['quiz_title']) ?></h2>
<form action="submit_quiz.php" method="POST">
    <input type="hidden" name="quiz_id" value="<?= $quizId ?>">

    <?php foreach ($questions as $i => $q): ?>
        <p><strong>Q<?= $i + 1 ?>: <?= htmlspecialchars($q['question_text']) ?></strong></p>
        <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="a" required> <?= htmlspecialchars($q['option_a']) ?></label><br>
        <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="b"> <?= htmlspecialchars($q['option_b']) ?></label><br>
        <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="c"> <?= htmlspecialchars($q['option_c']) ?></label><br>
        <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="d"> <?= htmlspecialchars($q['option_d']) ?></label><br><br>
    <?php endforeach; ?>

    <button type="submit">Submit Quiz</button>
</form>
