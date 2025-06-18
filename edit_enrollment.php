<?php
// edit_enrollment.php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Get enrollment ID from query string
$enrollmentId = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch the specific enrollment
try {
    $sql = "SELECT * FROM enrollments WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $enrollmentId]);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Process the form if it's submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $courseId = $_POST['course_id'];
    $enrollmentDate = $_POST['enrollment_date'];

    try {
        // Update enrollment in the database
        $sql = "UPDATE enrollments SET user_id = :user_id, course_id = :course_id, enrollment_date = :enrollment_date WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => $enrollmentDate,
            'id' => $enrollmentId
        ]);

        header('Location: view_enrollments.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Enrollment</title>
</head>
<body>

<h2>Edit Enrollment</h2>
<form action="edit_enrollment.php?id=<?= $enrollmentId ?>" method="POST">
    <label for="user_id">User ID:</label>
    <input type="text" name="user_id" value="<?= $enrollment['user_id'] ?>" required><br><br>
    
    <label for="course_id">Course ID:</label>
    <input type="text" name="course_id" value="<?= $enrollment['course_id'] ?>" required><br><br>
    
    <label for="enrollment_date">Enrollment Date:</label>
    <input type="date" name="enrollment_date" value="<?= $enrollment['enrollment_date'] ?>" required><br><br>
    
    <button type="submit">Update Enrollment</button>
</form>

</body>
</html>
