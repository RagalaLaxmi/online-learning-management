<?php
session_start();
require 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit;
}

$teacherId = $_SESSION['teacher_id'];

// Handle delete request (via GET param delete_id)
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    // Delete assignment by id (you can add ownership check here if needed)
    $stmtDel = $pdo->prepare("DELETE FROM assignments WHERE id = ?");
    $stmtDel->execute([$deleteId]);
    header("Location: view_assignments.php");
    exit;
}

// Fetch assignments with course name and user name
$stmt = $pdo->prepare("
    SELECT a.*, c.course_name, u.username AS assigned_user
    FROM assignments a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.due_date ASC
");
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script>
      function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this assignment?")) {
          window.location.href = "view_assignments.php?delete_id=" + id;
        }
      }
    </script>
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">📄 View Assignments</h2>
 <a href="add_assignment.php" class="btn btn-success">➕ Add Assignment</a>
    <?php if (empty($assignments)): ?>
        <p>No assignments found.</p>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Course</th>
                    <th>Assigned User</th>
                    <th>Attachment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($a['description'])) ?></td>
                        <td><?= htmlspecialchars($a['due_date']) ?></td>
                        <td><?= htmlspecialchars($a['course_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($a['assigned_user'] ?? 'N/A') ?></td>
                        <td>
                            <?php if (!empty($a['file_path']) && file_exists($a['file_path'])): ?>
                                <a href="<?= htmlspecialchars($a['file_path']) ?>" target="_blank">View File</a>
                            <?php else: ?>
                                No attachment
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_assignment.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <button onclick="confirmDelete(<?= $a['id'] ?>)" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
