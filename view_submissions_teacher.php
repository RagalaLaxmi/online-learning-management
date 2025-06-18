<?php
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit;
}

$teacherId = $_SESSION['teacher_id'];

$pdo = new PDO("mysql:host=localhost;dbname=lms_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission to update grade and feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
    $submissionId = $_POST['submission_id'];
    $grade = trim($_POST['grade']);
    $feedback = trim($_POST['feedback']);

    $update = $pdo->prepare("UPDATE submissions SET grade = ?, feedback = ? WHERE id = ?");
    $update->execute([$grade, $feedback, $submissionId]);
}

// Fetch all submissions for this teacher’s assignments
$stmt = $pdo->prepare("
    SELECT 
        s.id AS submission_id,
        s.file_path,
        s.submitted_at,
        s.grade,
        s.feedback,
        u.username,
        a.title AS assignment_title,
        c.course_name
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN courses c ON a.course_id = c.id
    JOIN users u ON s.user_id = u.id
    WHERE c.teacher_id = ?
    ORDER BY s.submitted_at DESC
");
$stmt->execute([$teacherId]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submitted Assignments with Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">📥 Submitted Assignments (Feedback & Grading)</h2>

    <?php if ($submissions): ?>
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Assignment</th>
                    <th>Submitted</th>
                    <th>File</th>
                    <th>Grade</th>
                    <th>Feedback</th>
                    <th>Save</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $sub): ?>
                    <tr>
                        <form method="POST">
                            <td><?= htmlspecialchars($sub['username']) ?></td>
                            <td><?= htmlspecialchars($sub['course_name']) ?></td>
                            <td><?= htmlspecialchars($sub['assignment_title']) ?></td>
                            <td><?= htmlspecialchars($sub['submitted_at']) ?></td>
                            <td>
                                <?php if ($sub['file_path']): ?>
                                    <a href="<?= htmlspecialchars($sub['file_path']) ?>" class="btn btn-sm btn-outline-primary" download>📎 Download</a>
                                <?php else: ?>
                                    No File
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="text" name="grade" class="form-control form-control-sm" value="<?= htmlspecialchars($sub['grade']) ?>" placeholder="A+" />
                            </td>
                            <td>
                                <textarea name="feedback" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($sub['feedback']) ?></textarea>
                            </td>
                            <td>
                                <input type="hidden" name="submission_id" value="<?= $sub['submission_id'] ?>" />
                                <button type="submit" class="btn btn-sm btn-success">💾 Save</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No submissions found.</div>
    <?php endif; ?>
</div>
</body>
</html>
