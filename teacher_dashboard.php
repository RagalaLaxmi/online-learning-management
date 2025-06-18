
<?php
session_start(); // Start the session

// Check if the user is logged in and has the role of 'teacher'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php'); // Redirect to login page if not logged in as teacher
    exit;
}
?>








<!DOCTYPE html>
<html lang="en">
   



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles10.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Teacher Dashboard</h2>
        <ul>
            <li><a href="view_course1.php">Manage Courses</a></li>

            <li><a href="view_assignments.php">Manage Assignments</a></li>
            <li><a href="view_quizzes.php">Manage Quizzes</a></li>
            <li><a href="view_student_progress.php">View Student Progress</a></li>
            <li><a href="view_Grade_Assignments.php">Grade Assignments</a></li>


            <li><a href="send Announcements.php">Send Announcements</a></li>

           <li><a href="student_interaction.php">Student Interaction</a></li>



            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h1>Welcome to the Teacher Dashboard</h1>
        <p>Use the Manage courses, Manage Quizzes,View Student Progress, Grade Assignments, Send Announcements, Student Interaction.</p>
    </div>

</body>
</html>

