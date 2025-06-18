<?php
// add_enrollment1.php

session_start();

// Allow only logged-in users or admins
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'user'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Get all available courses
try {
    $courses = $pdo->query("SELECT id, course_name FROM courses")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['role'] === 'user') {
        $userId = $_SESSION['user_id']; // Users can only enroll themselves
    } else {
        $userId = $_POST['user_id']; // Admins can enroll anyone
    }

    $courseId = $_POST['course_id'];
    $enrollmentDate = $_POST['enrollment_date'];

    try {
        $sql = "INSERT INTO enrollments (user_id, course_id, enrollment_date) 
                VALUES (:user_id, :course_id, :enrollment_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => $enrollmentDate
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
    <title>Add Enrollment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #333;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Enrollment</h2>

    <form action="add_enrollment.php" method="POST">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <label for="user_id">User ID:</label>
            <input type="text" name="user_id" id="user_id" required>
        <?php else: ?>
            <!-- Hidden field for user -->
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <?php endif; ?>

        <label for="course_id">Select Course:</label>
        <select name="course_id" id="course_id" required>
            <option value="">-- Select a course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="enrollment_date">Enrollment Date:</label>
        <input type="date" name="enrollment_date" id="enrollment_date" required>

        <button type="submit">Add Enrollment</button>
    </form>

    

</body>
</html>
