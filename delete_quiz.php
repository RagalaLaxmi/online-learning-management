<?php
session_start();
require 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT id FROM questions WHERE quiz_id = ?");
    $stmt->execute([$id]);
    $questions = $stmt->fetchAll();

    foreach ($questions as $q) {
        $pdo->prepare("DELETE FROM options WHERE question_id = ?")->execute([$q['id']]);
    }

    $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$id]);

    header("Location: quizzes.php");
    exit;
}
