<?php
session_start();

// Check user session and role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User ID not found in session. Please <a href='login.php'>login again</a>.</p>";
    exit;
}

$userId = $_SESSION['user_id'];

// DB connection
$host = 'localhost';
$dbname = 'lms_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles20.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="#profile" onclick="showSection('profile')">Profile</a></li>
                <li><a href="#my-courses" onclick="showSection('my-courses')">My Courses</a></li>
              
<li><a href="view_assignments_user.php">Assignments</a></li>


                <li><a href="#quizzes" onclick="showSection('quizzes')">Quizzes</a></li>
                <li><a href="#notifications" onclick="showSection('notifications')">Notifications</a></li>
 
<li><a href="add_enrollment1.php">Enrollements</a></li>

                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Welcome to Your Dashboard</h1>

            <!-- Profile Section -->
            <div id="profile" class="content-section">
                <h2>User Profile</h2>
                <?php
                $stmt = $pdo->prepare("SELECT username, email, profile_picture, last_login FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();

                if ($user):
                ?>
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-img-container">
                            <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
                        </div>
                        <h3><?= htmlspecialchars($user['username']) ?></h3>
                        <p class="email"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div class="profile-body">
                        <p><strong>User ID:</strong> <?= htmlspecialchars($userId); ?></p>
                        <p><strong>Last Login:</strong> <?= htmlspecialchars($user['last_login']); ?></p>
                    </div>
                    <div class="profile-actions">
                        <a href="edit_profile.php"><button>Edit Profile</button></a>
                        <form method="POST" action="delete_profile.php" onsubmit="return confirm('Are you sure you want to delete your account?');">
                            <button type="submit" name="delete">Delete Account</button>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                    <p>Error loading profile information.</p>
                <?php endif; ?>
            </div>

            <!-- My Courses Section -->
            <div id="my-courses" class="content-section" style="display:none;">
                <h2>Enrolled Courses</h2>
                <ul id="course-list">
                    <?php
                    $stmt = $pdo->prepare("SELECT c.course_name, e.enrollment_date 
                                           FROM enrollments e 
                                           JOIN courses c ON e.course_id = c.id 
                                           WHERE e.user_id = ?");
                    $stmt->execute([$userId]);
                    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($courses) {
                        foreach ($courses as $course) {
                            echo "<li><strong>" . htmlspecialchars($course['course_name']) . "</strong> (Enrolled on: " . $course['enrollment_date'] . ")</li>";
                        }
                    } else {
                        echo "<li>You are not enrolled in any courses yet.</li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Assignments Section -->
            <div id="assignments" class="content-section" style="display:none;">
                <h2>Assignments</h2>
                <ul id="assignment-list">
 <?php
        // Fetch assignments related to the user’s courses (based on enrollments)
        $stmt = $pdo->prepare("
            SELECT a.id, a.title, a.description, a.due_date, c.course_name
            FROM assignments a
            JOIN courses c ON a.course_id = c.id
            JOIN enrollments e ON e.course_id = c.id
            WHERE e.user_id = ?
            ORDER BY a.due_date ASC
        ");
        $stmt->execute([$userId]);
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($assignments) {
            foreach ($assignments as $assignment) {
                echo "<li>";
                echo "<strong>" . htmlspecialchars($assignment['title']) . "</strong>";
                echo " (Course: " . htmlspecialchars($assignment['course_name']) . ")";
                echo "<br>Due Date: " . htmlspecialchars($assignment['due_date']);
                echo "<br>" . nl2br(htmlspecialchars($assignment['description']));
                echo "</li>";
            }
        } else {
            echo "<li>No assignments found.</li>";
        }
        ?>
                   
                </ul>
            </div>

            <!-- Quizzes Section -->
            <!-- <div id="quizzes" class="content-section" style="display:none;">
                <h2>Quizzes</h2>
                <ul id="quiz-list">
                    <li>No quizzes available yet.</li>
                </ul>
            </div>-->
 <div id="quizzes" class="content-section" style="display:none;">
                <h2>Available Quizzes</h2>
                <ul id="quiz-list">
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT q.id AS quiz_id, q.quiz_title, c.course_name
                        FROM quizzes q
                        JOIN courses c ON q.course_id = c.id
                        JOIN enrollments e ON e.course_id = c.id
                        WHERE e.user_id = ?
                    ");
                    $stmt->execute([$userId]);
                    $quizzes = $stmt->fetchAll();

                    if ($quizzes):
                        foreach ($quizzes as $quiz):
                    ?>
                        <li>
                            <strong><?= htmlspecialchars($quiz['quiz_title']) ?></strong> 
                            (Course: <?= htmlspecialchars($quiz['course_name']) ?>)
                            - <a href="take_quiz.php?quiz_id=<?= $quiz['quiz_id'] ?>">Take Quiz</a>
                        </li>
                    <?php
                        endforeach;
                    else:
                        echo "<li>No quizzes available yet.</li>";
                    endif;
                    ?>
                </ul>
            </div>


            <!-- Notifications Section -->
            <div id="notifications" class="content-section" style="display:none;">
                <h2>Notifications</h2>
                <ul id="notification-list">
                    <?php
    $stmt = $pdo->query("SELECT title, message, created_at FROM announcements ORDER BY created_at DESC");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($announcements):
        foreach ($announcements as $note):
    ?>
            <li>
                <strong><?= htmlspecialchars($note['title']) ?></strong><br>
                <?= nl2br(htmlspecialchars($note['message'])) ?><br>
                <small><em>Posted on: <?= $note['created_at'] ?></em></small>
            </li>
    <?php
        endforeach;
    else:
        echo "<li>No new announcements.</li>";
    endif;
    ?>
                </ul>
            </div>
        </main>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');

            // Show the selected section
            const selectedSection = document.getElementById(sectionId);
            selectedSection.style.display = 'block';
        }
    </script>
</body>
</html> 