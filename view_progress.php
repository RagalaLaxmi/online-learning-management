<?php
include 'db.php';

// Fetch student progress data
$query = "SELECT sp.progress_percentage, s.name AS student_name, c.name AS course_name
          FROM student_progress sp
          JOIN students s ON sp.student_id = s.id
          JOIN courses c ON sp.course_id = c.id";
$stmt = $pdo->query($query);
$progress = $stmt->fetchAll();
?>

<div class="view-progress">
    <h2>View Student Progress</h2>
    <div id="student-progress-list">
        <!-- Dynamic Student Progress List will appear here -->
    </div>
</div>
