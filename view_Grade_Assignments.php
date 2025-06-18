<?php
session_start();
require 'db.php';

// Only teachers can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// Fetch all assignment submissions from users with role 'user'
$stmt = $pdo->prepare("
    SELECT s.id AS submission_id, s.assignment_id, s.user_id, s.submitted_text, s.submitted_file, s.submitted_at, s.grade,
           a.title AS assignment_title,
           u.name AS student_name
    FROM assignment_submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN users u ON s.student_id = u.id
    WHERE u.role = 'user'
");
$stmt->execute();
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Assignments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5fa;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        th {
            background: #f0f0f0;
        }

        form {
            display: flex;
            gap: 10px;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
        }

        button {
            padding: 6px 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Grade Student Assignments</h2>

<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Assignment</th>
            <th>Submission</th>
            <th>Submitted At</th>
            <th>Grade</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($submissions) > 0): ?>
            <?php foreach ($submissions as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['assignment_title']) ?></td>
                    <td>
                        <?php if ($row['submitted_file']): ?>
                            <a href="uploads/<?= $row['submitted_file'] ?>" target="_blank">Download File</a>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars($row['submitted_text'])) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['submitted_at'] ?></td>
                    <td><?= $row['grade'] !== null ? $row['grade'] : 'Not graded' ?></td>
                    <td>
                        <form method="POST" action="grade_submission.php">
                            <input type="hidden" name="submission_id" value="<?= $row['submission_id'] ?>">
                            <input type="number" name="grade" min="0" max="100" required>
                            <button type="submit">Submit</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No submissions found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
