<?php
session_start();
require 'db.php';

// ✅ Check login
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Fetch all courses (added by any teacher or admin)
$courses = $pdo->query("SELECT id, course_name FROM courses")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch users with role 'user'
$stmtUsers = $pdo->prepare("SELECT id, username FROM users WHERE role = 'user'");
$stmtUsers->execute();
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $course_id = $_POST['course_id'];
    $user_id = $_POST['user_id'];
    $filePath = null;

    // ✅ Validate course exists
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    if ($stmt->rowCount() === 0) {
        die("Invalid course selected.");
    }

    // ✅ Validate user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'user'");
    $stmt->execute([$user_id]);
    if ($stmt->rowCount() === 0) {
        die("Invalid user selected.");
    }

    // ✅ Handle file upload
    if (!empty($_FILES['attachment']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES["attachment"]["name"]);
        $filePath = $uploadDir . time() . "_" . preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $fileName);

        if (!move_uploaded_file($_FILES["attachment"]["tmp_name"], $filePath)) {
            die("Failed to upload file.");
        }
    }

    // ✅ Insert into assignments
    $stmt = $pdo->prepare("INSERT INTO assignments (title, description, due_date, course_id, file_path, user_id)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $due_date, $course_id, $filePath, $user_id]);

    header("Location: view_assignments.php");
    exit;
}
?>

<!-- ✅ HTML PART STARTS HERE -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">📝 Add New Assignment</h2>

    <form method="POST" enctype="multipart/form-data" class="form-control p-4">
        <div class="mb-3">
            <label class="form-label">Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description:</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Due Date:</label>
            <input type="date" name="due_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Course:</label>
            <select name="course_id" class="form-select" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= htmlspecialchars($course['id']) ?>">
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Assign to User:</label>
            <select name="user_id" class="form-select" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Attach File (optional):</label>
            <input type="file" name="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">➕ Add Assignment</button>
    </form>
</div>
</body>
</html>
