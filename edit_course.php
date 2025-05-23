<?php
require_once 'includes/db.php';
session_start();

$courseId = $_POST['courseId'];
$userId = $_POST['userId'];
$courseName = $_POST['courseName'];
$adminUsername = $_POST['adminUsername'];
$adminPassword = $_POST['adminPassword'] ?? '';

// Validate input
if (empty($courseId) || empty($userId) || empty($courseName) || empty($adminUsername)) {
    $_SESSION['error'] = "All fields except password are required.";
    header("Location: courses.php");
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Update course name
    $stmt = $pdo->prepare("UPDATE courses SET course_name = ? WHERE id = ?");
    $stmt->execute([$courseName, $courseId]);

    // Update admin username
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$adminUsername, $userId]);

    // Optional: update password if provided
    if (!empty($adminPassword)) {
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
    }

    $pdo->commit();
    $_SESSION['success'] = "Course updated successfully.";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Update failed: " . $e->getMessage();
}

header("Location: courses.php");
exit;
