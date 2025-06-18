
<?php
include 'db.php';

// Fetch all assignments
$query = "SELECT * FROM assignments";
$stmt = $pdo->query($query);
$assignments = $stmt->fetchAll();

// Fetch all students
$studentsQuery = "SELECT * FROM students";
$studentsStmt = $pdo->query($studentsQuery);
$students = $studentsStmt->fetchAll();

// Fetch existing grades
$gradesQuery = "SELECT a.title, s.name AS student_name, g.grade
                FROM grades g
                JOIN assignments a ON g.assignment_id = a.id
                JOIN students s ON g.student_id = s.id";
$gradesStmt = $pdo->query($gradesQuery);
$grades = $gradesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_POST['student_id'];
    $grade = $_POST['grade'];

    // Insert or update grade for the student
    $stmt = $pdo->prepare("INSERT INTO grades (assignment_id, student_id, grade)
                           VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE grade = ?");
    $stmt->execute([$assignment_id, $student_id, $grade, $grade]);
    header("Location: grade_assignments.php");
}
?>


<div class="grade-assignments">
    <h2>Grade Assignments</h2>
    <div id="assignment-list">
        <!-- Dynamic Assignment List -->
    </div>
</div>
