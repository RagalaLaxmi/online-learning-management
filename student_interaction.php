<?php
session_start();

// Ensure only teachers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Interaction</title>
    <link rel="stylesheet" href="style_interaction.css">
</head>
<body>
    <div class="container">
        <h2>Send Email to User</h2>
        <form action="send_message.php" method="POST">
            <label for="user_email">User Email:</label>
            <input type="email" name="user_email" id="user_email" required>

            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" required>

            <label for="message">Message:</label>
            <textarea name="message" id="message" rows="6" required></textarea>

            <button type="submit">Send Email</button>
        </form>
    </div>
</body>
</html>
