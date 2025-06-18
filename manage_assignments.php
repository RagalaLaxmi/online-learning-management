<?php
include 'db.php';

// Fetch all assignments
$query = "SELECT * FROM assignments";
$stmt = $pdo->query($query);
$assignments = $stmt->fetchAll();

// Fetch courses for assignment creation
$coursesQuery = "SELECT * FROM courses";
$coursesStmt = $pdo->query($coursesQuery);
$courses = $coursesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $course_id = $_POST['course_id'];
    $due_date = $_POST['due_date'];

    // Insert new assignment into the database
    $stmt = $pdo->prepare("INSERT INTO assignments (course_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$course_id, $title, $description, $due_date]);
    header("Location: manage_assignments.php");
}
?>


<div class="manage-assignments">
    <h2>Manage Assignments</h2>
    <button onclick="showCreateAssignmentForm()">Create New Assignment</button>

    <div id="create-assignment-form" style="display:none;">
        <h3>Create Assignment</h3>
        <form id="create-assignment">
            <label for="assignment-title">Title:</label>
            <input type="text" id="assignment-title" name="title" required>

            <label for="assignment-description">Description:</label>
            <textarea id="assignment-description" name="description" required></textarea>

            <label for="assignment-course">Course:</label>
            <select id="assignment-course" name="course_id">
                <!-- Dynamic Courses -->
            </select>

            <button type="submit">Create Assignment</button>
        </form>
    </div>

    <div id="assignments-list">
        <!-- Dynamic Assignment List will appear here -->
    </div>
</div>
