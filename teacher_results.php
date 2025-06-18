<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$sql = "SELECT r.*, u.username, q.quiz_title, c.course_name
        FROM results r
        JOIN users u ON r.user_id = u.id
        JOIN quizzes q ON r.quiz_id = q.id
        JOIN courses c ON q.course_id = c.id
        ORDER BY r.taken_at DESC";

$results = $pdo->query($sql)->fetchAll();
?>

<h2>All Student Quiz Results</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Quiz</th>
            <th>Score</th>
            <th>Total Questions</th>
            <th>Percentage</th>
            <th>Taken At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['username']) ?></td>
                <td><?= htmlspecialchars($r['course_name']) ?></td>
                <td><?= htmlspecialchars($r['quiz_title']) ?></td>
                <td><?= $r['score'] ?></td>
                <td><?= $r['total'] ?></td>
                <td><?= round($r['percentage'], 2) ?>%</td>
                <td><?= $r['taken_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
