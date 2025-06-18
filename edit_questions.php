<?php
session_start();
require 'db.php';

// Ensure the teacher is logged in
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$quizId = $_GET['quiz_id'] ?? null; // Get quiz ID from URL
if (!$quizId) {
    die("Quiz ID is missing.");
}

$teacherId = $_SESSION['teacher_id'];

// Verify the quiz belongs to the logged-in teacher
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND course_id IN (SELECT id FROM courses WHERE teacher_id = ?)");
$stmt->execute([$quizId, $teacherId]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Unauthorized access to this quiz.");
}

// Fetch questions related to the quiz
$questionsStmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$questionsStmt->execute([$quizId]);
$questions = $questionsStmt->fetchAll();

// Form submission for different actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Edit Quiz Title and Description
    if (isset($_POST['edit_quiz'])) {
        $quizTitle = htmlspecialchars($_POST['quiz_title']);
        $quizDescription = htmlspecialchars($_POST['quiz_description']);

        $stmt = $pdo->prepare("UPDATE quizzes SET quiz_title = ?, description = ? WHERE id = ?");
        $stmt->execute([$quizTitle, $quizDescription, $quizId]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }

    // Add a new question
    if (isset($_POST['add_question'])) {
        $questionText = htmlspecialchars($_POST['question_text']);
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
        $stmt->execute([$quizId, $questionText]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }

    // Edit existing question
    if (isset($_POST['edit_question'])) {
        $questionId = $_POST['question_id'];
        $questionText = htmlspecialchars($_POST['question_text']);
        $stmt = $pdo->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
        $stmt->execute([$questionText, $questionId]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }

    // Delete question and its options
    if (isset($_POST['delete_question'])) {
        $questionId = $_POST['question_id'];

        // Delete associated options first
        $stmt = $pdo->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->execute([$questionId]);

        // Delete the question
        $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([$questionId]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }

    // Add a new option to a question
    if (isset($_POST['add_option'])) {
        $questionId = $_POST['question_id'];
        $optionText = htmlspecialchars($_POST['new_option_text']);
        $isCorrect = isset($_POST['new_is_correct']) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
        $stmt->execute([$questionId, $optionText, $isCorrect]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }

    // Edit option text and correctness
    if (isset($_POST['edit_option'])) {
        $optionId = $_POST['option_id'];
        $optionText = htmlspecialchars($_POST['option_text']);
        $isCorrect = isset($_POST['is_correct']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE id = ?");
        $stmt->execute([$optionText, $isCorrect, $optionId]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }

    // Delete option
    if (isset($_POST['delete_option'])) {
        $optionId = $_POST['option_id'];

        $stmt = $pdo->prepare("DELETE FROM options WHERE id = ?");
        $stmt->execute([$optionId]);

        header("Location: edit_questions.php?quiz_id=$quizId");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz - <?= htmlspecialchars($quiz['quiz_title']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
        }
        h2, h3, h4, h5 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            display: inline-block;
        }
        .question-section {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .option-section {
            margin-top: 10px;
            padding-left: 20px;
        }
        .option-section input[type="text"] {
            width: 80%;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .delete-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Quiz: <?= htmlspecialchars($quiz['quiz_title']) ?></h2>

    <!-- Edit Quiz Title and Description -->
    <form method="POST">
        <label>Quiz Title:</label><br>
        <input type="text" name="quiz_title" value="<?= htmlspecialchars($quiz['quiz_title']) ?>" required><br>

        <label>Quiz Description:</label><br>
        <textarea name="quiz_description" required><?= htmlspecialchars($quiz['description']) ?></textarea><br><br>

        <button type="submit" name="edit_quiz">Update Quiz</button>
    </form>

    <h3>Manage Questions</h3>
    <!-- Add New Question -->
    <form method="POST">
        <label>New Question:</label><br>
        <textarea name="question_text" placeholder="Enter new question" required></textarea><br>
        <button type="submit" name="add_question">Add Question</button>
    </form>

    <h3>Existing Questions</h3>
    <?php foreach ($questions as $question): ?>
        <div class="question-section">
            <h4>Question: <?= htmlspecialchars($question['question_text']) ?></h4>

            <!-- Edit Question -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                <textarea name="question_text"><?= htmlspecialchars($question['question_text']) ?></textarea><br>
                <button type="submit" name="edit_question">Update Question</button>
            </form>

            <!-- Delete Question -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                <button type="submit" name="delete_question" class="delete-btn" onclick="return confirm('Are you sure you want to delete this question?')">Delete Question</button>
            </form>

            <h5>Options for this question:</h5>
            <?php
            // Fetch options for this question
            $optionsStmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ?");
            $optionsStmt->execute([$question['id']]);
            $options = $optionsStmt->fetchAll();
            ?>

            <?php foreach ($options as $option): ?>
                <div class="option-section">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                        <input type="hidden" name="option_id" value="<?= $option['id'] ?>">
                        <input type="text" name="option_text" value="<?= htmlspecialchars($option['option_text']) ?>" required>
                        <label>
                            <input type="checkbox" name="is_correct" <?= $option['is_correct'] ? 'checked' : '' ?>>
                            Correct Answer
                        </label><br>
                        <button type="submit" name="edit_option">Update Option</button>
                    </form>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                        <input type="hidden" name="option_id" value="<?= $option['id'] ?>">
                        <button type="submit" name="delete_option" class="delete-btn" onclick="return confirm('Are you sure you want to delete this option?')">Delete Option</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <h5>Add New Option</h5>
            <form method="POST">
                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                <input type="text" name="new_option_text" placeholder="New Option Text" required><br>
                <label>
                    <input type="checkbox" name="new_is_correct">
                    Correct Answer
                </label><br>
                <button type="submit" name="add_option">Add Option</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
