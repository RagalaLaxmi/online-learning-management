<?php
session_start();

// Redirect to login if not logged in or not a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Include the database connection (PDO)
require_once 'db.php';

// SQL query to fetch student progress using 'username'
$sql = "SELECT users.username AS student_name, 
               courses.title AS course_title, 
               progress.percentage, 
               progress.grade 
        FROM progress 
        JOIN users ON progress.user_id = users.id 
        JOIN courses ON progress.course_id = courses.id 
        WHERE users.role = 'student'
        ORDER BY users.username";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Progress</title>
    <link rel="stylesheet" href="styles10.css">
    <style>
        .main-content {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            margin-top: 0;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h1>Student Progress</h1>

    <?php if ($results): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Username</th>
                    <th>Course Title</th>
                    <th>Progress (%)</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['course_title']) ?></td>
                        <td><?= htmlspecialchars($row['percentage']) ?>%</td>
                        <td><?= htmlspecialchars($row['grade']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No student progress data found.</p>
    <?php endif; ?>
</div>

</body>
</html>
