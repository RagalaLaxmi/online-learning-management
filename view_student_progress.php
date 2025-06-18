<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$sql = "SELECT 
            id,
            user_id,
            quiz_id,
            score,
            total,
            percentage,
            taken_at
        FROM results";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f5f9;
            margin: 0;
            padding: 0;
        }

        .main-content {
            width: 90%;
            max-width: 1000px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #eef3fb;
        }

        td {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Quiz Results</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Quiz ID</th>
                <th>Score</th>
                <th>Total</th>
                <th>Percentage</th>
                <th>Taken At</th>
            </tr>

            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['quiz_id']) ?></td>
                        <td><?= htmlspecialchars($row['score']) ?></td>
                        <td><?= htmlspecialchars($row['total']) ?></td>
                        <td><?= htmlspecialchars($row['percentage']) ?>%</td>
                        <td><?= htmlspecialchars($row['taken_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No quiz results found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
