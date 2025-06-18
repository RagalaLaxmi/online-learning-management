<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=lms_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get all assignments assigned to this user
$stmt = $pdo->prepare("
    SELECT a.*, c.course_name,
        (SELECT file_path FROM submissions WHERE user_id = ? AND assignment_id = a.id LIMIT 1) AS submitted_file
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    WHERE a.user_id = ?
    ORDER BY a.due_date ASC
");
$stmt->execute([$userId, $userId]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">📄 My Assignments & Submissions</h2>

    <?php if ($assignments): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due</th>
                    <th>Download</th>
                    <th>My Submission</th>
                    <th>Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['course_name']) ?></td>
                        <td><?= htmlspecialchars($a['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($a['description'])) ?></td>
                        <td><?= htmlspecialchars($a['due_date']) ?></td>
                        <td>
                            <?php if ($a['file_path']): ?>
                                <a href="<?= htmlspecialchars($a['file_path']) ?>" download>📥</a>
                            <?php else: ?> No file <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($a['submitted_file']): ?>
                                <a href="<?= htmlspecialchars($a['submitted_file']) ?>" download>✅ Submitted</a>
                            <?php else: ?>
                                ❌ Not Submitted
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$a['submitted_file']): ?>
                                <form method="POST" action="submit_assignment.php" enctype="multipart/form-data">
                                    <input type="hidden" name="assignment_id" value="<?= $a['id'] ?>">
                                    <input type="file" name="submission_file" required class="form-control mb-1" />
                                    <button type="submit" class="btn btn-sm btn-success">Upload</button>
                                </form>
                            <?php else: ?>
                                <small>Already submitted</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No assignments found.</p>
    <?php endif; ?>
</div>
</body>
</html>
