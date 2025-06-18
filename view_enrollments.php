<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'user'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

try {
    if ($_SESSION['role'] === 'admin') {
        // Admin sees all enrollments
        $sql = "SELECT e.id, u.username, c.course_name, e.enrollment_date
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } else {
        // Users see only their own enrollments
        $sql = "SELECT e.id, u.username, c.course_name, e.enrollment_date
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
    }

    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Enrollments</title>
    <link rel="stylesheet" href="styles50.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .add-btn {
            display: <?= ($_SESSION['role'] === 'admin') ? 'block' : 'none' ?>;
            margin: 20px auto;
            width: 200px;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .add-btn:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td a {
            text-decoration: none;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 3px;
        }

        td a.edit {
            background-color: #4CAF50;
            color: white;
        }

        td a.delete {
            background-color: #f44336;
            color: white;
        }

        td a:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1><?= $_SESSION['role'] === 'admin' ? 'Manage All Enrollments' : 'My Enrollments' ?></h1>

        <!-- Add Button (Admins Only) -->
        <a href="add_enrollment.php" class="add-btn">Add New Enrollment</a>

        <table>
            <tr>
                <th>Enrollment ID</th>
                <th>User</th>
                <th>Course</th>
                <th>Enrollment Date</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>

            <?php if ($enrollments): ?>
                <?php foreach ($enrollments as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td><?= htmlspecialchars($row['enrollment_date']) ?></td>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <td>
                                <a href="edit_enrollment.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                                <a href="delete_enrollment.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this enrollment?')">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="<?= ($_SESSION['role'] === 'admin') ? 5 : 4 ?>">No enrollments found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>
