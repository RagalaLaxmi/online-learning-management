<?php
require_once 'db.php';
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

$teacher_id = $_SESSION['username']; // Assuming the teacher's username is the ID (can be modified if needed)

// Handle form submission to create a course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $video_path = '';

    // Handle video upload
    if (isset($_FILES['course_video']) && $_FILES['course_video']['error'] == 0) {
        $file_tmp = $_FILES['course_video']['tmp_name'];
        $file_name = $_FILES['course_video']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['mp4', 'avi', 'mov', 'mkv'];

        if (in_array($file_ext, $allowed_ext)) {
            $video_path = 'uploads/' . uniqid() . '.' . $file_ext;  // Generate a unique file name
            move_uploaded_file($file_tmp, $video_path);  // Move file to the uploads directory
        } else {
            $error = "Invalid video file type. Only MP4, AVI, MOV, and MKV are allowed.";
        }
    }

    if (!isset($error)) {
        // Insert course details into the database
        $stmt = $pdo->prepare("INSERT INTO courses (course_name, course_description, video_path, teacher_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_name, $course_description, $video_path, $teacher_id]);

        header('Location: view_course1.php');
        exit;
    }
}

// Fetch available courses for the logged-in teacher
$stmt = $pdo->prepare("SELECT * FROM courses WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="stylest.css">
</head>
<body>
    <div class="container">
        <h1>Create New Course</h1>
        
        <!-- Course Creation Form -->
        <form method="POST" enctype="multipart/form-data">
            <label for="course_name">Course Name:</label>
            <input type="text" name="course_name" required><br><br>

            <label for="course_description">Course Description:</label>
            <textarea name="course_description" required></textarea><br><br>

            <label for="course_video">Upload Course Video:</label>
            <input type="file" name="course_video" accept="video/*" required><br><br>

            <button type="submit">Create Course</button>
        </form>

        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <h2>Available Courses</h2>
        <!-- Display available courses -->
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Description</th>
                    <th>Video</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="4">No courses available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                            <td>
                                <?php if ($course['video_path']): ?>
                                    <a href="<?php echo $course['video_path']; ?>" target="_blank">View Video</a>
                                <?php else: ?>
                                    No video uploaded.
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_course.php?id=<?php echo $course['id']; ?>">Edit</a> | 
                                <a href="delete_course.php?id=<?php echo $course['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
